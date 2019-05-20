CLI_SERVICE := drupal
CLI_SHELL := bash
CLI_USER := drupal
DOCKER_PROJECT_ROOT := /var/www/drupal/public_html

PHONY += amazeeio-env
amazeeio-env: ## Print Amazee.io env variables
	$(call docker_run_cmd,printenv | grep AMAZEE)

PHONY += versions
versions: ## Show software versions inside the Drupal container
	$(call colorecho, "\nSoftware versions on ${RUN_ON}:\n")
	$(call docker_run_cmd,php -v && echo " ")
	$(call composer_on_${RUN_ON}, --version && echo " ")
	$(call drush_on_${RUN_ON},--version)
	$(call docker_run_cmd,echo 'NPM version: '|tr '\n' ' ' && npm --version)
	$(call docker_run_cmd,echo 'Yarn version: '|tr '\n' ' ' && yarn --version)
