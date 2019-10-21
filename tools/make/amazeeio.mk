ifeq ($(AMAZEEIO_LAGOON),yes)
	CLI_SERVICE := cli
else
	CLI_SERVICE := drupal
	CLI_USER := drupal
	DOCKER_PROJECT_ROOT := /var/www/drupal/public_html
endif

CLI_SHELL := bash

PHONY += amazeeio-env
amazeeio-env: ## Print Amazee.io env variables
	$(call docker_run_cmd,printenv | grep AMAZEE)
