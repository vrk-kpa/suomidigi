DRUPAL_CONF_EXISTS := $(shell test -f conf/cmi/core.extension.yml && echo yes || echo no)
DRUPAL_FRESH_TARGETS := up build sync post-install
DRUPAL_NEW_TARGETS := up build drush-si drush-uli
DRUPAL_POST_INSTALL_TARGETS := drush-updb drush-cim drush-uli
DRUPAL_SYNC_FILES := yes
DRUPAL_SYNC_SOURCE ?= production
DRUPAL_VERSION ?= 8
SYNC_TARGETS += drush-sync

ifdef DRUPAL_WEBROOT
	WEBROOT := $(DRUPAL_WEBROOT)
endif

PHONY += drush-cex
drush-cex: ## Export configuration
ifeq (${DRUPAL_VERSION},7)
	$(call warn,\"drush cex\" is not Drupal 7 command\n)
else
	$(call step,Export configuration (${RUN_ON})...)
	$(call drush_on_${RUN_ON},cex -y)
endif

PHONY += drush-cim
drush-cim: ## Import configuration
ifeq (${DRUPAL_VERSION},7)
	$(call warn,\"drush cim\" is not Drupal 7 command\n)
else
	$(call step,Import configuration (${RUN_ON})...)
	$(call drush_on_${RUN_ON},cim -y)
endif

PHONY += drush-cc
drush-cc: drush-cr ## Clear caches (alias for drush-cr)

PHONY += drush-cr
drush-cr: ## Clear caches
	$(call step,Clearing caches...)
ifeq (${DRUPAL_VERSION},7)
	$(call drush_on_${RUN_ON},cc all)
else
	$(call drush_on_${RUN_ON},cr)
endif

PHONY += drush-status
drush-status: ## Show Drupal status information
	$(call drush_on_${RUN_ON},status)

PHONY += drush-uli
drush-uli: ## Get login link
	$(call step,Login to your site with:)
	$(call drush_on_${RUN_ON},uli)

PHONY += drush-si
ifeq ($(DRUPAL_CONF_EXISTS)$(DRUPAL_VERSION),yes8)
    drush-si: DRUSH_SI := -y --existing-config
else
    drush-si: DRUSH_SI := -y minimal
endif
drush-si: ## Site install
	$(call drush_on_${RUN_ON},si ${DRUSH_SI})

PHONY += drush-updb
drush-updb: ## Run database updates
	$(call step,Run database updates...)
	$(call drush_on_${RUN_ON},updb -y)

PHONY += fresh
fresh: ## Build fresh development environment and sync
	@$(MAKE) $(DRUPAL_FRESH_TARGETS)

PHONY += new
new: ## Create a new empty Drupal installation from configuration
	@$(MAKE) $(DRUPAL_NEW_TARGETS)

PHONY += post-install
post-install: ## Run post-install Drush actions
	@$(MAKE) $(DRUPAL_POST_INSTALL_TARGETS)

PHONY += drush-sync
drush-sync: ## Sync database and files
	$(call step,Sync database from @$(DRUPAL_SYNC_SOURCE)...)
	$(call drush_on_${RUN_ON},sql-sync -y @$(DRUPAL_SYNC_SOURCE) @self)
ifeq ($(DRUPAL_SYNC_FILES),yes)
	$(call step,Sync files from @$(DRUPAL_SYNC_SOURCE)...)
	$(call drush_on_${RUN_ON},-y rsync --mode=akzu @$(DRUPAL_SYNC_SOURCE):%files @self:%files)
endif

define drush_on_docker
	$(call docker_run_cmd,cd ${DOCKER_PROJECT_ROOT}/${WEBROOT} && drush --ansi --strict=0 $(1))
endef

define drush_on_host
	@drush -r $$PWD/${WEBROOT} --ansi --strict=0 $(1)
endef
