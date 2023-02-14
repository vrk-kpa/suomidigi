THEME_PATH := $(shell pwd)/$(WEBROOT)/themes/custom/suomidigi

PHONY += drupal-build-theme
drupal-build-theme:
	$(call step,Build theme with Gulp...\n)
	@docker run -it --rm -v $(THEME_PATH):/data druidfi/donn:node-$(NODE_VERSION) gulp production

PHONY += drupal-watch-theme
drupal-watch-theme:
	$(call step,Watch theme with Gulp...\n)
	@docker run -it --rm -v $(THEME_PATH):/data druidfi/donn:node-$(NODE_VERSION) gulp watch
