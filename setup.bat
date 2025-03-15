@echo off
echo Creating folder structure for CDK Bojonegoro Dashboard...
echo.

rem Create main directories
mkdir admin
mkdir admin\assets
mkdir admin\assets\css
mkdir admin\assets\js
mkdir admin\assets\images
mkdir admin\assets\vendors
mkdir admin\includes
mkdir admin\uploads
mkdir admin\uploads\publikasi
mkdir admin\uploads\galeri
mkdir admin\uploads\dokumen

echo Folder structure created.
echo.
echo Creating main PHP files...

rem Create includes files
echo ^<?php // Database and configuration file^> > admin\includes\config.php
echo ^<?php // Helper functions^> > admin\includes\functions.php
echo ^<?php // Authentication functions^> > admin\includes\auth.php
echo ^<?php // Header template^> > admin\includes\header.php
echo ^<?php // Footer template^> > admin\includes\footer.php
echo ^<?php // Sidebar navigation template^> > admin\includes\sidebar.php

rem Create main PHP pages
echo ^<?php // Dashboard home page^> > admin\index.php
echo ^<?php // Login page^> > admin\login.php
echo ^<?php // Logout script^> > admin\logout.php

rem Create module files
echo ^<?php // Manage layanan^> > admin\layanan.php
echo ^<?php // Add/edit layanan form^> > admin\layanan-form.php
echo ^<?php // Manage program^> > admin\program.php
echo ^<?php // Add/edit program form^> > admin\program-form.php
echo ^<?php // Manage publikasi^> > admin\publikasi.php
echo ^<?php // Add/edit publikasi form^> > admin\publikasi-form.php
echo ^<?php // Manage galeri^> > admin\galeri.php
echo ^<?php // Add/edit galeri form^> > admin\galeri-form.php
echo ^<?php // Manage dokumen^> > admin\dokumen.php
echo ^<?php // Add/edit dokumen form^> > admin\dokumen-form.php
echo ^<?php // Manage statistik^> > admin\statistik.php
echo ^<?php // Add/edit statistik form^> > admin\statistik-form.php
echo ^<?php // Manage users^> > admin\users.php
echo ^<?php // Add/edit users form^> > admin\user-form.php
echo ^<?php // Site settings^> > admin\pengaturan.php

rem Create CSS and JS files
echo /* Main dashboard styles */ > admin\assets\css\dashboard.css
echo /* Login page styles */ > admin\assets\css\login.css
echo // Main dashboard scripts > admin\assets\js\dashboard.js
echo // Form validation and AJAX scripts > admin\assets\js\forms.js

echo.
echo Files created successfully!
echo.
echo Setup completed. You can now populate the PHP files with appropriate code.
echo.
pause