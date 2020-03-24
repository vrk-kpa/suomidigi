DRUPAL_POST_INSTALL_TARGETS += enable-stagefileproxy

PHONY += enable-stagefileproxy

enable-stagefileproxy: ## Enable and configure proxy
ifneq ($(ENV),production)
	$(call step, Enabling Stage File Proxy)
	$(call drush_on_${RUN_ON}, -y en stage_file_proxy)
	$(call drush_on_${RUN_ON}, -y config-set stage_file_proxy.settings origin "https://www.suomidigi.fi")
endif


