<?php
define('APP_ENV', getenv('APP_ENV') ? getenv('APP_ENV')  : 'production');
define('MAIN_DB_ADAPTER', 'masterDbAdapter');
define('EXTENSIONS_TABLE', 'pw_extensions');
define('EXTENSIONS_INSTALLATION_TABLE', 'pw_extension_installation');