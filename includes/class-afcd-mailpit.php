<?php
/**
 * The adjustments for the Mailpit container functionality.
 *
 * @since      1.0.1
 * @package    AFCD_Core_Development
 * @subpackage AFCD_Core_Development/includes
 * @author     Andrea Fercia
 *
 * Note: This implementation requires the Mailpit container to be running and
 * accessible at 'mailpit:1025'. Use your docker-compose.override.yml to
 * configure the Mailpit service if needed. Example:
 *
 * ```yaml
 * docker-compose.override.yml:
 * services:
 *  mailpit:
 *  container_name: mailpit
 *  image: axllent/mailpit:latest
 *  restart: unless-stopped
 *  ports:
 *     - '1025:1025'
 *     - '8025:8025'
 *  networks:
 *     - wpdevnet
 * ```
 */
class AFCD_Mailpit implements AFCD_Integration_Interface {

	/**
	 * Registers the hooks for this integration.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		// 1. Force WordPress to generate a valid domain pattern for the From address.
		add_filter(
			'wp_mail_from',
			function () {
				return 'wordpress@localhost.local';
			}
		);

		// Routes local core development environment emails to the Mailpit container.
		add_action(
			'phpmailer_init',
			function ( $phpmailer ) {
				$phpmailer->isSMTP();
				$phpmailer->Host       = 'mailpit';
				$phpmailer->Port       = 1025;
				$phpmailer->SMTPAuth   = false;
				$phpmailer->SMTPSecure = false;
			}
		);
	}
}
