DRUPAL_CONF_EXISTS := $(shell test -f conf/cmi/core.extension.yml && echo yes || echo no)
DRUPAL_FRESH_TARGETS := up build sync post-install
DRUPAL_NEW_TARGETS := up build drush-si drush-uli
ifeq ($(DRUPAL_VERSION),7)
DRUPAL_POST_INSTALL_TARGETS := drush-updb drush-cr
DRUPAL_ENABLE_MODULES ?= no
else
DRUPAL_POST_INSTALL_TARGETS := drush-deploy
CLEAN_FOLDERS += ${WEBROOT}/core
CLEAN_FOLDERS += ${WEBROOT}/libraries
CLEAN_FOLDERS += ${WEBROOT}/modules/contrib
CLEAN_FOLDERS += ${WEBROOT}/profiles/contrib
CLEAN_FOLDERS += ${WEBROOT}/themes/contrib
DRUPAL_DISABLE_MODULES ?= no
DRUPAL_ENABLE_MODULES ?= no
endif
DRUPAL_PROFILE ?= minimal
DRUPAL_SYNC_FILES ?= yes
DRUPAL_SYNC_SOURCE ?= production
DRUPAL_VERSION ?= 9

DRUSH_RSYNC_MODE ?= Pakzu
DRUSH_RSYNC_OPTS ?=  -- --omit-dir-times --no-perms --no-group --no-owner --chmod=ugo=rwX
DRUSH_RSYNC_EXCLUDE ?= css:ctools:js:php:tmp:tmp_php

SYNC_TARGETS += drush-sync

CS_EXTS := inc,php,module,install,profile,theme
CS_STANDARD_PATHS := vendor/drupal/coder/coder_sniffer
CS_STANDARDS := Drupal,DrupalPractice
LINT_PATHS_JS += ./$(WEBROOT)/modules/custom/*/js
LINT_PATHS_JS += ./$(WEBROOT)/themes/custom/*/js
LINT_PATHS_PHP += drush
LINT_PATHS_PHP += $(WEBROOT)/modules/custom
LINT_PATHS_PHP += $(WEBROOT)/themes/custom
LINT_PHP_TARGETS += lint-drupal
FIX_TARGETS += fix-drupal

ifeq ($(GH_DUMP_ARTIFACT),yes)
	DRUPAL_FRESH_TARGETS := gh-download-dump $(DRUPAL_FRESH_TARGETS)
endif

ifneq ($(DRUPAL_DISABLE_MODULES),no)
	SYNC_TARGETS += drush-disable-modules
endif

ifneq ($(DRUPAL_ENABLE_MODULES),no)
	DRUPAL_POST_INSTALL_TARGETS += drush-enable-modules
endif

PHONY += drupal-update
drupal-update: ## Update Drupal core with Composer
	$(call step,Update Drupal core with Composer...\n)
	$(call composer,update -W "drupal/core-*")

PHONY += drush-cex
drush-cex: ## Export configuration
ifeq ($(DRUPAL_VERSION),7)
	$(call warn,\"drush cex\" is not Drupal 7 command\n)
else
	$(call step,Export configuration...\n)
	$(call drush,cex -y)
endif

PHONY += drush-cim
drush-cim: ## Import configuration
ifeq ($(DRUPAL_VERSION),7)
	$(call warn,\"drush cim\" is not Drupal 7 command\n)
else
	$(call step,Import configuration...\n)
	$(call drush,cim -y)
endif

PHONY += drush-cc
drush-cc: drush-cr

PHONY += drush-cr
drush-cr: ## Clear caches
	$(call step,Clearing caches...\n)
ifeq ($(DRUPAL_VERSION),7)
	$(call drush,cc all)
else
	$(call drush,cr)
endif

PHONY += drush-status
drush-status: ## Show Drupal status information
	$(call drush,status)

PHONY += drush-uli
drush-uli: DRUPAL_UID ?=
drush-uli: ## Get login link
	$(call step,Login to your site with:\n)
ifeq ($(DRUPAL_VERSION),7)
	$(call drush,uli)
else
	$(call drush,uli$(if $(DRUPAL_UID), --uid=$(DRUPAL_UID),) admin/reports/status)
endif

PHONY += drush-si
ifeq ($(DRUPAL_CONF_EXISTS)$(DRUPAL_VERSION),yes9)
    drush-si: DRUSH_SI := -y --existing-config
else
    drush-si: DRUSH_SI := -y $(DRUPAL_PROFILE)
endif
drush-si: ## Site install
	$(call drush,si ${DRUSH_SI})

PHONY += drush-deploy
drush-deploy: ## Run Drush deploy
	$(call step,Run Drush deploy...\n)
	$(call drush,deploy)

PHONY += drush-updb
drush-updb: ## Run database updates
	$(call step,Run database updates...\n)
	$(call drush,updb -y)

PHONY += fresh
fresh: ## Build fresh development environment and sync
	@$(MAKE) $(DRUPAL_FRESH_TARGETS)

PHONY += new
new: ## Create a new empty Drupal installation from configuration
	@$(MAKE) $(DRUPAL_NEW_TARGETS)

PHONY += post-install
post-install: ## Run post-install Drush actions
	@$(MAKE) $(DRUPAL_POST_INSTALL_TARGETS) drush-uli

PHONY += drush-disable-modules
drush-disable-modules: ## Disable Drupal modules
	$(call step,Disable Drupal modules...\n)
ifneq ($(DRUPAL_DISABLE_MODULES),no)
	$(call drush,pmu -y $(subst ",,$(DRUPAL_DISABLE_MODULES)))
else
	$(call sub_step,No modules to disable)
endif

PHONY += drush-enable-modules
drush-enable-modules: ## Enable Drupal modules
	$(call step,Enable Drupal modules...\n)
ifneq ($(DRUPAL_ENABLE_MODULES),no)
	$(call drush,en -y $(subst ",,$(DRUPAL_ENABLE_MODULES)))
else
	$(call sub_step,No modules to enable)
endif

PHONY += drush-sync
drush-sync: drush-sync-db drush-sync-files ## Sync database and files

PHONY += drush-sync-db
drush-sync-db: ## Sync database
ifeq ($(DUMP_SQL_EXISTS),yes)
	$(call step,Import local SQL dump...)
	$(call drush,sql-query --file=${DOCKER_PROJECT_ROOT}/$(DUMP_SQL_FILENAME),SQL dump imported)
else
	$(call step,Sync database from @$(DRUPAL_SYNC_SOURCE)...)
ifeq ($(DRUPAL_VERSION),7)
	$(call drush,sql-drop -y)
endif
	$(call drush,sql-sync -y --structure-tables-key=common @$(DRUPAL_SYNC_SOURCE) @self)
endif

PHONY += drush-sync-files
drush-sync-files: ## Sync files
ifeq ($(DRUPAL_SYNC_FILES),yes)
	$(call step,Sync files from @$(DRUPAL_SYNC_SOURCE)...)
ifeq ($(DRUPAL_VERSION),7)
	@chmod 0755 ${WEBROOT}/sites/default
	@mkdir -p ${WEBROOT}/sites/default/files
	@chmod 0777 ${WEBROOT}/sites/default/files
	$(call drush,-y rsync --exclude-paths=$(DRUSH_RSYNC_EXCLUDE) --mode=$(DRUSH_RSYNC_MODE) @$(DRUPAL_SYNC_SOURCE):%files @self:%files)
else
	$(call drush,-y rsync --exclude-paths=$(DRUSH_RSYNC_EXCLUDE) --mode=$(DRUSH_RSYNC_MODE) @$(DRUPAL_SYNC_SOURCE):%files @self:%files $(DRUSH_RSYNC_OPTS))
endif
endif

PHONY += drush-create-dump
drush-create-dump: FLAGS := --structure-tables-key=common --extra-dump=--no-tablespaces
drush-create-dump: ## Create database dump to dump.sql
	$(call drush,sql-dump $(FLAGS) --result-file=${DOCKER_PROJECT_ROOT}/$(DUMP_SQL_FILENAME))

PHONY += drush-download-dump
drush-download-dump: ## Download database dump to dump.sql
	$(call drush,-Dssh.tty=0 @$(DRUPAL_SYNC_SOURCE) sql-dump --structure-tables-key=common > ${DOCKER_PROJECT_ROOT}/$(DUMP_SQL_FILENAME))

PHONY += open-db-gui
open-db-gui: DB_CONTAINER := $(COMPOSE_PROJECT_NAME)-db
open-db-gui: DB_NAME := drupal
open-db-gui: DB_USER := drupal
open-db-gui: DB_PASS := drupal
open-db-gui: --open-db-gui ## Open database with GUI tool

PHONY += fix-drupal
fix-drupal: PATHS := $(subst $(space),,$(LINT_PATHS_PHP))
fix-drupal: ## Fix Drupal code style
	$(call step,Fix Drupal code style with phpcbf...\n)
	$(call cs,phpcbf,$(PATHS))

PHONY += lint-drupal
lint-drupal: PATHS := $(subst $(space),,$(LINT_PATHS_PHP))
lint-drupal: ## Lint Drupal code style
	$(call step,Lint Drupal code style with phpcs...\n)
	$(call cs,phpcs,$(PATHS))

PHONY += mmfix
mmfix: MODULE := MISSING_MODULE
mmfix:
	$(call step,Remove missing module '$(MODULE)')
ifeq ($(DRUPAL_VERSION),7)
	$(call drush,sql-query \"DELETE from system where type = 'module' AND name = '$(MODULE)';\",Module was removed)
else
	$(call drush,sql-query \"DELETE FROM key_value WHERE collection='system.schema' AND name='$(MODULE)';\",Module was removed)
endif

ifeq ($(RUN_ON),docker)
ifeq ($(DRUPAL_VERSION),7)
define drush
	$(call docker_run_cmd,cd ${DOCKER_PROJECT_ROOT}/${WEBROOT} && drush --ansi --strict=0 $(1),$(2))
endef
else
define drush
	$(call docker_run_cmd,drush --ansi --strict=0 $(1),$(2))
endef
endif
else
define drush
	@cd $(COMPOSER_JSON_PATH)/${WEBROOT} && drush --ansi --strict=0 $(1)
endef
endif
