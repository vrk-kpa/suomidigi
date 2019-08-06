TEST_TARGETS += lint
LINT_PATHS_JS += ./$(DRUPAL_WEBROOT)/modules/custom/*/js
LINT_PATHS_JS += ./$(DRUPAL_WEBROOT)/themes/custom/*/js

PHONY += lint
lint: lint-php lint-js ## Check code style

PHONY += lint-js
lint-js: DOCKER_NODE_IMG ?= node:8.16.0-alpine
lint-js: WD := /usr/src/app
lint-js: ## Check code style for JS files
	$(call colorecho, "\nInstall linters...\n")
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) yarn --cwd $(DRUPAL_WEBROOT)/core install
	$(call colorecho, "\nCheck code style for JS files: $(DRUPAL_LINT_PATHS)\n")
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) \
		$(DRUPAL_WEBROOT)/core/node_modules/eslint/bin/eslint.js --color --ignore-pattern '**/vendor/*' \
		--c ./$(DRUPAL_WEBROOT)/core/.eslintrc.json --global nav,moment,responsiveNav:true $(LINT_PATHS_JS)

PHONY += lint-php
lint-php: ## Check code style for PHP files
	$(call colorecho, "\nCheck code style for PHP files...\n")
	$(call composer_on_${RUN_ON},lint)

PHONY += test
test: ## Run tests
	$(call colorecho, "\nRun tests:")
	$(call colorecho, "- Following test targets will be run: $(TEST_TARGETS)")
	@$(MAKE) $(TEST_TARGETS)
	$(call colorecho, "\nTests completed.")

PHONY += test-foobar
test-foobar:
	$(call test_result,test-foobar,"[OK]")

define test_result
	@echo "- ${YELLOW}${1}:${NO_COLOR} ${GREEN}${2}${NO_COLOR}"
endef
