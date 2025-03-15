<?php
/**
 * Helper Functions
 * CDK Wilayah Bojonegoro
 */

// Include configuration
require_once 'config.php';

/**
 * Generate a URL-friendly slug from a string
 * 
 * @param string $string Input string
 * @return string Slug
 */
function createSlug($string)
{
    // Convert to lowercase and replace spaces with hyphens
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $string), '-'));

    // Remove duplicate hyphens
    $slug = preg_replace('/-+/', '-', $slug);

    return $slug;
}

/**
 * Generate a unique slug by checking against existing slugs in a table
 * 
 * @param string $string Original string
 * @param string $table Table to check uniqueness against
 * @param string $field Field name for the slug
 * @param int $exclude_id ID to exclude (for updates)
 * @return string Unique slug
 */
function createUniqueSlug($string, $table, $field = 'slug', $exclude_id = null)
{
    $conn = getConnection();

    // Create initial slug
    $slug = createSlug($string);
    $originalSlug = $slug;
    $counter = 1;

    // Check if slug exists
    while (true) {
        $query = "SELECT $field FROM $table WHERE $field = '$slug'";

        // Exclude the current item (for updates)
        if ($exclude_id !== null) {
            $query .= " AND id != $exclude_id";
        }

        $result = $conn->query($query);

        // If slug is unique, return it
        if ($result && $result->num_rows === 0) {
            return $slug;
        }

        // Otherwise, add a counter and try again
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
}

/**
 * Format date to Indonesian format
 * 
 * @param string $date Date in Y-m-d format
 * @param bool $withTime Include time
 * @return string Formatted date
 */
function formatDate($date, $withTime = false)
{
    if (empty($date))
        return '';

    $months = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    // Parse date
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);

    $formatted = "$day $month $year";

    // Add time if requested
    if ($withTime) {
        $formatted .= ' ' . date('H:i', $timestamp);
    }

    return $formatted;
}

/**
 * Format file size to human-readable format
 * 
 * @param int $bytes File size in bytes
 * @param int $precision Decimal precision
 * @return string Formatted file size
 */
function formatFileSize($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Extract an excerpt from HTML content
 * 
 * @param string $content HTML content
 * @param int $length Maximum length in characters
 * @return string Plain text excerpt
 */
function getExcerpt($content, $length = 150)
{
    // Remove HTML tags and decode entities
    $text = strip_tags($content);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    // Trim to specified length
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length) . '...';
    }

    return $text;
}

/**
 * Upload a file
 * 
 * @param array $file $_FILES array item
 * @param string $destination Destination directory
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @param bool $rename_if_exists Rename file if it exists
 * @return array|bool Array with file info or false on failure
 */
function uploadFile($file, $destination, $allowed_types = [], $max_size = MAX_FILE_SIZE, $rename_if_exists = true)
{
    // Check upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Check file size
    if ($file['size'] > $max_size) {
        return false;
    }

    // Check file type if specified
    if (!empty($allowed_types) && !in_array($file['type'], $allowed_types)) {
        return false;
    }

    // Create directory if it doesn't exist
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $file['name']);
    $filename = strtolower($filename);

    // Generate unique filename if needed
    $filepath = $destination . $filename;
    if ($rename_if_exists && file_exists($filepath)) {
        $info = pathinfo($filename);
        $filename = $info['filename'] . '-' . date('YmdHis') . '.' . $info['extension'];
        $filepath = $destination . $filename;
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'name' => $filename,
            'path' => $filepath,
            'url' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $filepath),
            'type' => $file['type'],
            'size' => $file['size']
        ];
    }

    return false;
}

/**
 * Delete a file
 * 
 * @param string $filepath Path to the file
 * @return bool True on success, false on failure
 */
function deleteFile($filepath)
{
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }

    return false;
}

/**
 * Sanitize input to prevent XSS
 * 
 * @param string $input Input string
 * @return string Sanitized string
 */
function sanitizeInput($input)
{
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Display flash message and clear it
 * 
 * @param string $key Message key in session
 * @return string|null HTML for message or null if no message
 */
function flashMessage($key = 'message')
{
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);

        $type = 'info';

        if ($key === 'error') {
            $type = 'danger';
        } elseif ($key === 'success') {
            $type = 'success';
        } elseif ($key === 'warning') {
            $type = 'warning';
        }

        return "<div class='alert alert-$type'>$message</div>";
    }

    return null;
}

/**
 * Set flash message
 * 
 * @param string $message Message content
 * @param string $type Message type (success, error, warning, info)
 * @return void
 */
function setFlashMessage($message, $type = 'message')
{
    $_SESSION[$type] = $message;
}

/**
 * Check if current URL matches a given pattern
 * 
 * @param string $pattern URL pattern to check
 * @return bool True if matched, false otherwise
 */
function isActiveUrl($pattern)
{
    $currentPage = basename($_SERVER['PHP_SELF']);

    if ($pattern === $currentPage) {
        return true;
    }

    if (strpos($pattern, '*') !== false) {
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match('/^' . $pattern . '$/', $currentPage);
    }

    return false;
}

/**
 * Get pagination HTML
 * 
 * @param int $page Current page
 * @param int $total_pages Total number of pages
 * @param string $url_pattern URL pattern with placeholder for page
 * @return string Pagination HTML
 */
function getPagination($page, $total_pages, $url_pattern = '?page={page}')
{
    if ($total_pages <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation"><ul class="pagination">';

    // Previous page link
    if ($page > 1) {
        $prev_url = str_replace('{page}', $page - 1, $url_pattern);
        $html .= '<li class="page-item"><a class="page-link" href="' . $prev_url . '">&laquo; Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }

    // Page links
    $start = max(1, $page - 2);
    $end = min($total_pages, $page + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', 1, $url_pattern) . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $i, $url_pattern) . '">' . $i . '</a></li>';
        }
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . str_replace('{page}', $total_pages, $url_pattern) . '">' . $total_pages . '</a></li>';
    }

    // Next page link
    if ($page < $total_pages) {
        $next_url = str_replace('{page}', $page + 1, $url_pattern);
        $html .= '<li class="page-item"><a class="page-link" href="' . $next_url . '">Next &raquo;</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}

/**
 * Generate Select Options from an array
 * 
 * @param array $options Array of options (key => value)
 * @param mixed $selected Selected value(s)
 * @param bool $multiple Whether it's a multiple select
 * @return string HTML options
 */
function generateSelectOptions($options, $selected = null, $multiple = false)
{
    $html = '';

    // Convert selected to array for multiple select
    if ($multiple && !is_array($selected)) {
        $selected = (array) $selected;
    }

    foreach ($options as $value => $label) {
        $is_selected = false;

        if ($multiple && is_array($selected)) {
            $is_selected = in_array($value, $selected);
        } else {
            $is_selected = ($value == $selected);
        }

        $selected_attr = $is_selected ? ' selected' : '';
        $html .= "<option value=\"{$value}\"{$selected_attr}>{$label}</option>";
    }

    return $html;
}

/**
 * Get data for a specific item from a table
 * 
 * @param string $table Table name
 * @param int $id Item ID
 * @param string $id_field Name of ID field (default: 'id')
 * @return array|null Item data or null if not found
 */
function getItemById($table, $id, $id_field = 'id')
{
    $conn = getConnection();
    $id = (int) $id;

    $query = "SELECT * FROM $table WHERE $id_field = $id LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        return $result->fetch_assoc();
    }

    return null;
}

/**
 * Check if a value exists in a table field
 * 
 * @param string $table Table name
 * @param string $field Field name
 * @param mixed $value Value to check
 * @param int $exclude_id ID to exclude (for updates)
 * @return bool True if exists, false otherwise
 */
function valueExists($table, $field, $value, $exclude_id = null)
{
    $conn = getConnection();

    // Sanitize inputs
    $table = $conn->real_escape_string($table);
    $field = $conn->real_escape_string($field);
    $value = $conn->real_escape_string($value);

    $query = "SELECT 1 FROM $table WHERE $field = '$value'";

    // Exclude current ID for updates
    if ($exclude_id !== null) {
        $exclude_id = (int) $exclude_id;
        $query .= " AND id != $exclude_id";
    }

    $query .= " LIMIT 1";
    $result = $conn->query($query);

    return ($result && $result->num_rows === 1);
}

/**
 * Get count of rows from a table with optional conditions
 * 
 * @param string $table Table name
 * @param string $conditions SQL conditions (without WHERE)
 * @return int Count
 */
function getCount($table, $conditions = '')
{
    $conn = getConnection();

    $query = "SELECT COUNT(*) AS total FROM $table";

    if (!empty($conditions)) {
        $query .= " WHERE $conditions";
    }

    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    return 0;
}

/**
 * Create breadcrumbs HTML
 * 
 * @param array $items Breadcrumb items [label => url]
 * @return string HTML for breadcrumbs
 */
function breadcrumbs($items)
{
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

    $count = count($items);
    $i = 0;

    foreach ($items as $label => $url) {
        $i++;

        if ($i === $count) {
            // Last item (active)
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . sanitizeInput($label) . '</li>';
        } else {
            // Other items
            $html .= '<li class="breadcrumb-item"><a href="' . sanitizeInput($url) . '">' . sanitizeInput($label) . '</a></li>';
        }
    }

    $html .= '</ol></nav>';

    return $html;
}

/**
 * Get base URL
 * 
 * @return string Base URL
 */
function getBaseUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);

    // Remove 'admin' from the path when in admin area
    $script = str_replace('/admin', '', $script);

    // Ensure script path ends with a slash
    if ($script !== '/' && substr($script, -1) !== '/') {
        $script .= '/';
    }

    return $protocol . '://' . $host . $script;
}

/**
 * Get admin URL
 * 
 * @return string Admin URL
 */
function getAdminUrl()
{
    return getBaseUrl() . 'admin/';
}

/**
 * Validate allowed file types
 * 
 * @param string $filetype MIME type
 * @param string $type Type of file (image, document)
 * @return bool True if allowed, false otherwise
 */
function isAllowedFileType($filetype, $type = 'image')
{
    if ($type === 'image') {
        return in_array($filetype, ALLOWED_IMAGE_TYPES);
    } elseif ($type === 'document') {
        return in_array($filetype, ALLOWED_DOCUMENT_TYPES);
    }

    return false;
}