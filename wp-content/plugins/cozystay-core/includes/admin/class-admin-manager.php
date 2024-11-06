<?php
namespace LoftOcean;
if ( ! class_exists( 'Admin_Manager' ) ) {
	class Admin_Manager {
		/**
		* String root directory
		*/
		protected $dir = '';
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_print_styles', array( $this, 'print_admin_styles' ) );
			add_action( 'loftocean_ajax_pre_load', array( $this, 'load_ajax_files' ) );
		}
		/**
		* Init function
		*/
		public function init() {
			$this->setup_env();
			$this->load_files();
		}
		/**
		* Setup environment settings
		*/
		protected function setup_env() {
			$this->dir = LOFTOCEAN_DIR . 'includes/admin/';
		}
		/**
		* Load files
		*/
		protected function load_files() {
			if ( $this->is_front() || $this->is_admin_edit_page() || wp_doing_ajax() ) {
				require_once $this->dir . 'class-meta-box.php';
				require_once $this->dir . 'class-post-formats.php';
			}
		}
		/**
		* Load ajax related files
		*/
		public function load_ajax_files() {
			require_once $this->dir . 'class-post-formats.php';
		}
		/**
		* Condition function to test if in admin post list page
		* @return boolean
		*/
		protected function is_admin_list_post() {
			global $pagenow;
			$is_list_page = is_admin() && ! empty( $pagenow ) && ( 'edit.php' === $pagenow ) && empty( $_GET['post_type'] );
			$is_ajax_saving = wp_doing_ajax() && ! empty( $_REQUEST['action'] ) && ( 'loftocean_featured_post' === sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) );
			return $is_list_page || $is_ajax_saving;
		}
		/**
		* Condition function to test if in admin post list page
		* @return boolean
		*/
		protected function is_admin_edit_page() {
			global $pagenow;
			return is_admin() && ! empty( $pagenow ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}
		/**
		* Condition function to test if in front
		* @return boolean
		*/
		protected function is_front() {
			global $pagenow;
			return ! is_admin() && ! empty( $pagenow ) && ( 'index.php' === $pagenow );
		}
		/**
		* Print CSS for admin sections
		*/
		public function print_admin_styles() {
			echo '<style type="text/css" id="loftocean-admin-footer-custom-css">body.wp-admin #wpfooter { position: static; }</style>';
		}
	}
	new Admin_Manager();
}
