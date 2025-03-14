<?php
/**
 * Database Connection untuk website CDK Wilayah Bojonegoro
 */

// Pastikan file config sudah diinclude
if (!defined('DB_HOST')) {
    require_once 'config.php';
}

/**
 * Fungsi untuk mendapatkan koneksi database
 * 
 * @return mysqli Object koneksi database
 */
function getDbConnection()
{
    static $conn = null;

    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Periksa koneksi
        if ($conn->connect_error) {
            if (DEBUG) {
                die("Koneksi database gagal: " . $conn->connect_error);
            } else {
                die("Koneksi database gagal. Silakan hubungi administrator.");
            }
        }

        // Set charset ke UTF-8
        $conn->set_charset("utf8");
    }

    return $conn;
}

/**
 * Jalankan query dan return hasilnya
 * 
 * @param string $query Query SQL
 * @param array $params Parameter untuk prepared statement
 * @return mixed Object mysqli_result untuk SELECT, boolean untuk query lain
 */
function db_query($query, $params = [])
{
    $conn = getDbConnection();
    $result = false;

    try {
        if (count($params) > 0) {
            // Prepared statement
            $stmt = $conn->prepare($query);

            if ($stmt) {
                // Buat tipe parameter dan bind parameter
                $types = '';
                $bindParams = [];

                // Reference array untuk bind_param
                $bindParams[] = &$types;

                foreach ($params as $key => $param) {
                    if (is_int($param)) {
                        $types .= 'i'; // integer
                    } elseif (is_float($param)) {
                        $types .= 'd'; // double/float
                    } elseif (is_string($param)) {
                        $types .= 's'; // string
                    } else {
                        $types .= 'b'; // blob
                    }

                    $bindParams[] = &$params[$key];
                }

                // Bind parameter
                if (!empty($types)) {
                    call_user_func_array([$stmt, 'bind_param'], $bindParams);
                }

                // Execute
                $stmt->execute();

                // Get result untuk SELECT query
                if (stripos(trim($query), 'SELECT') === 0) {
                    $result = $stmt->get_result();
                } else {
                    $result = $stmt->affected_rows > 0;
                }

                $stmt->close();
            } else {
                throw new Exception("Prepared statement error: " . $conn->error);
            }
        } else {
            // Query biasa
            $result = $conn->query($query);

            if (!$result) {
                throw new Exception("Query error: " . $conn->error);
            }
        }
    } catch (Exception $e) {
        if (DEBUG) {
            echo "Database error: " . $e->getMessage();
        }
        error_log("Database error: " . $e->getMessage());
        $result = false;
    }

    return $result;
}

/**
 * Dapatkan satu baris hasil query
 * 
 * @param string $query Query SQL
 * @param array $params Parameter untuk prepared statement
 * @return array|boolean Array hasil query atau false jika tidak ada hasil
 */
function db_fetch($query, $params = [])
{
    $result = db_query($query, $params);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }

    return false;
}

/**
 * Dapatkan semua baris hasil query
 * 
 * @param string $query Query SQL
 * @param array $params Parameter untuk prepared statement
 * @return array Array hasil query atau array kosong jika tidak ada hasil
 */
function db_fetch_all($query, $params = [])
{
    $result = db_query($query, $params);
    $rows = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    return $rows;
}

/**
 * Insert data ke table
 * 
 * @param string $table Nama tabel
 * @param array $data Data yang akan diinsert (format: ['column' => 'value'])
 * @return int|boolean ID dari data yang baru diinsert atau false jika gagal
 */
function db_insert($table, $data)
{
    $conn = getDbConnection();
    $columns = [];
    $placeholders = [];
    $values = [];

    foreach ($data as $column => $value) {
        $columns[] = "`$column`";
        $placeholders[] = "?";
        $values[] = $value;
    }

    $columnsStr = implode(', ', $columns);
    $placeholdersStr = implode(', ', $placeholders);

    $query = "INSERT INTO `$table` ($columnsStr) VALUES ($placeholdersStr)";

    try {
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Buat tipe parameter
            $types = '';
            foreach ($values as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }

            // Bind parameter
            $stmt->bind_param($types, ...$values);

            // Execute
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $insert_id = $stmt->insert_id;
                $stmt->close();
                return $insert_id;
            } else {
                $stmt->close();
                return false;
            }
        } else {
            throw new Exception("Prepared statement error: " . $conn->error);
        }
    } catch (Exception $e) {
        if (DEBUG) {
            echo "Database error: " . $e->getMessage();
        }
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update data di table
 * 
 * @param string $table Nama tabel
 * @param array $data Data yang akan diupdate (format: ['column' => 'value'])
 * @param string $where Kondisi where (format: "column = ?")
 * @param array $params Parameter untuk kondisi where
 * @return boolean True jika berhasil, false jika gagal
 */
function db_update($table, $data, $where, $params = [])
{
    $conn = getDbConnection();
    $sets = [];
    $values = [];

    foreach ($data as $column => $value) {
        $sets[] = "`$column` = ?";
        $values[] = $value;
    }

    $setsStr = implode(', ', $sets);

    $query = "UPDATE `$table` SET $setsStr WHERE $where";

    try {
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Buat tipe parameter
            $types = '';
            foreach (array_merge($values, $params) as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }

            // Bind parameter
            $stmt->bind_param($types, ...array_merge($values, $params));

            // Execute
            $stmt->execute();

            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        } else {
            throw new Exception("Prepared statement error: " . $conn->error);
        }
    } catch (Exception $e) {
        if (DEBUG) {
            echo "Database error: " . $e->getMessage();
        }
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete data dari table
 * 
 * @param string $table Nama tabel
 * @param string $where Kondisi where (format: "column = ?")
 * @param array $params Parameter untuk kondisi where
 * @return boolean True jika berhasil, false jika gagal
 */
function db_delete($table, $where, $params = [])
{
    $conn = getDbConnection();

    $query = "DELETE FROM `$table` WHERE $where";

    try {
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Buat tipe parameter
            $types = '';
            foreach ($params as $value) {
                if (is_int($value)) {
                    $types .= 'i';
                } elseif (is_float($value)) {
                    $types .= 'd';
                } elseif (is_string($value)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }

            // Bind parameter
            $stmt->bind_param($types, ...$params);

            // Execute
            $stmt->execute();

            $result = $stmt->affected_rows > 0;
            $stmt->close();
            return $result;
        } else {
            throw new Exception("Prepared statement error: " . $conn->error);
        }
    } catch (Exception $e) {
        if (DEBUG) {
            echo "Database error: " . $e->getMessage();
        }
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}