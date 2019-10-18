TEST_TARGETS += lint
LINT_PATHS_JS += ./$(WEBROOT)/modules/custom/*/js
LINT_PATHS_JS += ./$(WEBROOT)/themes/custom/*/js

PHONY += lint
lint: lint-php lint-js ## Check code style

PHONY += lint-js
lint-js: DOCKER_NODE_IMG ?= node:8.16.0-alpine
lint-js: WD := /usr/src/app
lint-js: ## Check code style for JS files
	$(call step,Install linters...)
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) yarn --cwd $(WEBROOT)/core install
	$(call step,Check code style for JS files: $(DRUPAL_LINT_PATHS))
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) \
		$(WEBROOT)/core/node_modules/eslint/bin/eslint.js --color --ignore-pattern '**/vendor/*' \
		--c ./$(WEBROOT)/core/.eslintrc.json --global nav,moment,responsiveNav:true $(LINT_PATHS_JS)

PHONY += lint-php
lint-php: ## Check code style for PHP files
	$(call step,Check code style for PHP files...)
	$(call composer_on_${RUN_ON},lint)

PHONY += test
test: ## Run tests
	$(call step,Run tests:\n- Following test targets will be run: $(TEST_TARGETS))
	@$(MAKE) $(TEST_TARGETS)
	$(call step,Tests completed.)

PHONY += test-foobar
test-foobar:
	$(call test_result,test-foobar,"[OK]")

define test_result
	@echo "- ${YELLOW}${1}:${NO_COLOR} ${GREEN}${2}${NO_COLOR}"
endef
