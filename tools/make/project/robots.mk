ifeq (${ENV}, dev)
	BUILD_TARGETS += copy-robots
	PHONY += copy-robots
	ROBOTS_SOURCE := ./conf/env_development/robots.txt
	ROBOTS_EXIST := $(shell test -f $(ROBOTS_SOURCE) && echo yes || echo no)
endif

copy-robots: ## Copy robots.txt
	$(call colorecho, "\n-Copying robots.txt from ${ROBOTS_SOURCE} to ./public/robots.txt .\n")
	cp ${ROBOTS_SOURCE} ./public/robots.txt
