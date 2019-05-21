BUILD_TARGETS += copy-robots

PHONY += copy-robots
copy-robots: ## Copy robots.txt
ifeq ($(ENV),testing)
        $(call colorecho, "\n-Copying robots.txt for testing env.\n")
        cp ./conf/env_testing/robots.txt ./public/robots.txt
endif
