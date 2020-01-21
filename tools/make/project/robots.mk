BUILD_TARGETS += copy-robots

PHONY += copy-robots
copy-robots: ## Copy robots.txt
    $(call colorecho, "\n-Copying robots.txt for all environments.\n")
    cp ./conf/robots.txt ./public/robots.txt

