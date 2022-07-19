<?php 

return [

	/**
	 * Application url
	 */
	'url' => 'localhost',


	/**
	 * Version number will be appended to reload the in browser cached files in public/ressource/...
	 * Exemple: https://exemple.com/ressources/js/solo.min.js?v=1.5.23
	 */
	'version' => 1.0,


	/**
	 * Supported languages
	 * IMPORTANT: Every language added here must have a translation file in lang/[language].json
	 */
	'supported_lang' => ['en', 'fr', 'nl', 'it'],


	/**
	 * Default languages if none of the above languages are detected
	 */
	'default_lang' => 'en',


	/**
	 * Database credentials (mysql)
	 */
	'db_host' => 'localhost',
	'db_name' => 'clever',
	'db_user' => 'sammy',
	'db_pass' => 'password',


	/**
	 * Bcrypt cost
	 */
	'bcrypt' => 12,


	/**
	 * Value for cookie https only, easier for dev env.
	 * MUST BE TRUE IN PRODUCTION
	 */
	'cookie_secure' => true,
];