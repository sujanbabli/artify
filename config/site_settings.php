<?php
/**
 * Site Settings Configuration
 * 
 * This file stores dynamic site settings that can be changed through the admin panel
 */

// Email settings
$SITE_SETTINGS = [
    // Email Settings
    'email_notifications_enabled' => true,
    'email_notifications_to_admin' => true,
    'email_notifications_to_customer' => true,
    
    // Site Settings
    'maintenance_mode' => false,
    'maintenance_message' => 'Our site is currently undergoing maintenance. Please check back soon.',
    
    // Other settings can be added here
];

/**
 * Get a site setting value
 * @param string $key Setting key
 * @param mixed $default Default value if key doesn't exist
 * @return mixed Setting value
 */
function get_site_setting($key, $default = null) {
    global $SITE_SETTINGS;
    return isset($SITE_SETTINGS[$key]) ? $SITE_SETTINGS[$key] : $default;
}

/**
 * Update a site setting
 * @param string $key Setting key
 * @param mixed $value New value
 * @return boolean Success status
 */
function update_site_setting($key, $value) {
    global $SITE_SETTINGS;
    $SITE_SETTINGS[$key] = $value;
    
    // Build PHP code
    $code = "<?php\n/**\n * Site Settings Configuration\n * \n * This file stores dynamic site settings that can be changed through the admin panel\n */\n\n// Email settings\n\$SITE_SETTINGS = [\n";
    
    foreach ($SITE_SETTINGS as $k => $v) {
        if (is_bool($v)) {
            $code .= "    '$k' => " . ($v ? 'true' : 'false') . ",\n";
        } else if (is_string($v)) {
            $code .= "    '$k' => '" . str_replace("'", "\\'", $v) . "',\n";
        } else if (is_numeric($v)) {
            $code .= "    '$k' => $v,\n";
        } else {
            // Skip non-scalar values
            continue;
        }
    }
    
    $code .= "];\n\n";
    $code .= file_get_contents(__FILE__, false, null, strpos(file_get_contents(__FILE__), "/**\n * Get a site setting"));
    
    return file_put_contents(__FILE__, $code) !== false;
}
