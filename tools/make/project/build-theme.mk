ifeq ($(RUN_ON), docker)
	PATH_TO_THEME := ./themes/custom/
else
	PATH_TO_THEME := ./public/themes/custom/
endif

THEME_PACKAGE_JSON_EXISTS := $(shell test -f ./public/themes/custom/$(DRUPAL_THEME_NAME)/package.json && echo yes || echo no)

ifeq (${THEME_PACKAGE_JSON_EXISTS},yes)
	BUILD_TARGETS += build-theme
endif

ifeq ($(ENV),production)
	BUILD_THEME_ARGS := production
else
	BUILD_THEME_ARGS := development
endif

build-theme: package.json ## Install NPM packages and build theme
	$(call colorecho, "\n-Do npm install for theme on ${RUN_ON}...\n")
	$(call npm_on_${RUN_ON},install --prefix ${PATH_TO_THEME}/${DRUPAL_THEME_NAME} --engine-strict true)
	$(call colorecho, "\n-Build theme for ${BUILD_THEME_ARGS} on ${RUN_ON}...\n")
	$(call npm_on_${RUN_ON},run --prefix ${PATH_TO_THEME}/${DRUPAL_THEME_NAME} gulp ${BUILD_THEME_ARGS})
