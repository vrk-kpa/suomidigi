ARTIFACT_INCLUDE_EXISTS := $(shell test -f conf/artifact/include && echo yes || echo no)
ARTIFACT_EXCLUDE_EXISTS := $(shell test -f conf/artifact/exclude && echo yes || echo no)
ARTIFACT_CMD := tar -hczf artifact.tar.gz

ifeq ($(ARTIFACT_INCLUDE_EXISTS),yes)
	ARTIFACT_CMD := ${ARTIFACT_CMD} --files-from=conf/artifact/include
else
	ARTIFACT_CMD := ${ARTIFACT_CMD} *
endif

ifeq ($(ARTIFACT_EXCLUDE_EXISTS),yes)
	ARTIFACT_CMD := ${ARTIFACT_CMD} --exclude-from=conf/artifact/exclude
endif

PHONY += artifact
# This command can always be run on host
artifact: RUN_ON := host
artifact: build ## Make tar.gz package from the current build
	$(call colorecho, "\nCreate artifact (${RUN_ON}):\n")
	@${ARTIFACT_CMD}

PHONY += build
build: $(BUILD_TARGETS) ## Build codebase(s)
	$(call colorecho, "\nStart build for env: $(ENV)")
	$(call colorecho, "- Following targets will be run: $(BUILD_TARGETS)")
	@$(MAKE) $(BUILD_TARGETS) ENV=$(ENV)
	$(call colorecho, "\nBuild completed.")

PHONY += build-dev
build-dev: build

PHONY += build-testing
build-testing: ENV := testing
build-testing: build

PHONY += build-production
build-production: ENV := production
build-production: build

PHONY += clean
clean: ## Clean folders
	$(call colorecho, "\nClean folders: ${CLEAN_FOLDERS}")
	@rm -rf ${CLEAN_FOLDERS}

PHONY += help
help: ## List all make commands
	$(call colorecho, "\nAvailable make commands:")
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' | sort

PHONY += self-update
self-update: ## Self-update makefiles from druidfi/tools
	$(call colorecho, "Update makefiles from druidfi/tools")
	@bash -c "$$(curl -fsSL ${UPDATE_SCRIPT_URL})"

PHONY += self-update
sync: $(SYNC_TARGETS) ## Sync data from other environments
	$(call colorecho, "\nStart sync")
	$(call colorecho, "- Following targets will be run: $(SYNC_TARGETS)")
	@$(MAKE) $(SYNC_TARGETS) ENV=$(ENV)
	$(call colorecho, "\nSync completed.")
