SYNC_TARGETS += drush-sync
TEST_TARGETS += drupal-lint-js
DRUPAL_LINT_PATHS += ./$(DRUPAL_WEBROOT)/modules/custom/*/js
DRUPAL_LINT_PATHS += ./$(DRUPAL_WEBROOT)/themes/custom/*/js

PHONY += drupal-lint-js
drupal-lint-js: DOCKER_NODE_IMG ?= node:8.16.0-alpine
drupal-lint-js: WD := /usr/src/app
drupal-lint-js: ## Lint JS files
	$(call colorecho, "\nDo Yarn install...\n")
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) yarn --cwd $(DRUPAL_WEBROOT)/core install
	$(call colorecho, "\nLint JS files in: $(DRUPAL_LINT_PATHS)\n")
	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) \
		$(DRUPAL_WEBROOT)/core/node_modules/eslint/bin/eslint.js --color --ignore-pattern '**/vendor/*' \
		--c ./$(DRUPAL_WEBROOT)/core/.eslintrc.json --global nav,moment,responsiveNav:true $(DRUPAL_LINT_PATHS)

PHONY += drush-cex
drush-cex: ## Export configuration
	$(call colorecho, "\n- Export configuration (${RUN_ON})...\n")
ifeq (${DRUPAL_VERSION},7)
	@echo "- \"drush cex\" is not Drupal 7 command ${YELLOW}[warning]${NO_COLOR}"
else
	$(call drush_on_${RUN_ON},cex -y)
endif

PHONY += drush-cim
drush-cim: ## Import configuration
	$(call colorecho, "\n- Import configuration (${RUN_ON})...\n")
ifeq (${DRUPAL_VERSION},7)
	@echo "- \"drush cim\" is not Drupal 7 command ${YELLOW}[warning]${NO_COLOR}"
else
	$(call drush_on_${RUN_ON},cim -y)
endif

PHONY += drush-status
drush-status: ## Show Drupal status information
	$(call drush_on_${RUN_ON},status)

PHONY += drush-uli
drush-uli: ## Get login link
	$(call colorecho, "\n- Login to your site with:\n")
	$(call drush_on_${RUN_ON},uli)

PHONY += drush-si
ifeq ($(DRUPAL_VERSION),7)
    drush-si: DRUSH_SI := -y
else
    drush-si: DRUSH_SI := -y --existing-config
endif
drush-si: ## Site install
	$(call drush_on_${RUN_ON},si ${DRUSH_SI})

PHONY += drush-updb
drush-updb: ## Run database updates
	$(call colorecho, "\n- Run database updates...\n")
	$(call drush_on_${RUN_ON},updb -y)

PHONY += fresh
fresh: up build sync post-install ## Build fresh development environment and sync

PHONY += new
new: up build drush-si drush-uli ## Create a new empty Drupal installation from configuration

PHONY += post-install
post-install: drush-updb drush-cim drush-uli ## Run post-install Drush actions

PHONY += drush-sync
drush-sync: ## Sync database and files
	$(call colorecho, "\nSync database from @$(DRUPAL_SYNC_SOURCE)...\n")
	$(call drush_on_${RUN_ON},sql-sync -y @$(DRUPAL_SYNC_SOURCE) @self)
	$(call colorecho, "Sync files from @$(DRUPAL_SYNC_SOURCE)...\n")
	$(call drush_on_${RUN_ON},-y rsync --mode=akzu @$(DRUPAL_SYNC_SOURCE):%files @self:%files)

define drush_on_docker
	$(call docker_run_cmd,drush --ansi --strict=0 $(1))
endef

define drush_on_host
	@drush --ansi --strict=0 $(1)
endef
