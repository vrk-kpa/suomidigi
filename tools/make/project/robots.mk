BUILD_TARGETS += copy-robots

PHONY += copy-robots
copy-robots: ## Copy robots.txt
	$(call step, Copying robots.txt for all environments.)
	cp ./conf/robots.txt ./public/robots.txt
