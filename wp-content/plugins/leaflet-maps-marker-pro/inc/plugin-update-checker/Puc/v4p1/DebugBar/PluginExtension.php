<?php
if ( !class_exists('MMPPuc_v4p1_DebugBar_PluginExtension', false) ):

	class MMPPuc_v4p1_DebugBar_PluginExtension extends MMPPuc_v4p1_DebugBar_Extension {
		/** @var MMPPuc_v4p1_Plugin_UpdateChecker */
		protected $updateChecker;

		public function __construct($updateChecker) {
			parent::__construct($updateChecker, 'MMPPuc_v4p1_DebugBar_PluginPanel');
			add_action('wp_ajax_puc_v4_debug_request_info', array($this, 'ajaxRequestInfo'));
		}

		/**
		 * Request plugin info and output it.
		 */
		public function ajaxRequestInfo() {
			if ( $_POST['uid'] !== $this->updateChecker->getUniqueName('uid') ) {
				return;
			}
			$this->preAjaxReqest();
			$info = $this->updateChecker->requestInfo();
			if ( $info !== null ) {
				echo 'Successfully retrieved plugin info from the metadata URL:';
				echo '<pre>', htmlentities(print_r($info, true)), '</pre>';
			} else {
				echo 'Failed to retrieve plugin info from the metadata URL.';
			}
			exit;
		}
	}

endif;