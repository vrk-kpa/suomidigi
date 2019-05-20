ifeq ($(RUN_ON), docker)
	PATH_TO_THEME := ./themes/custom/
else
	PATH_TO_THEME := ./public/themes/custom/
endif

THEME_PACKAGE_JSON_EXISTS := $(shell test -f $(PATH_TO_THEME)/$(DRUPAL_THEME_NAME)/package.json && echo yes || echo no)

ifeq (${THEME_PACKAGE_JSON_EXISTS},yes)
	BUILD_TARGETS += theme_node_modules
endif

ifeq ($(ENV),production)
	BUILD_THEME_ARGS := production
else
	BUILD_THEME_ARGS := development
endif

theme_node_modules: package.json ## Install NPM packages
	$(call colorecho, "\n-Do npm install for theme on ${RUN_ON}...\n")
	$(call npm_on_${RUN_ON},install --prefix ${PATH_TO_THEME}/${DRUPAL_THEME_NAME} --engine-strict true)
	$(call colorecho, "\n-Build theme for ${BUILD_THEME_ARGS} on ${RUN_ON}...\n")
	$(call npm_on_${RUN_ON},run --prefix ${PATH_TO_THEME}/${DRUPAL_THEME_NAME} gulp ${BUILD_THEME_ARGS})
