DOCKER_COMPOSE_EXEC := docker-compose exec -T
DOCKER_WARNING_INSIDE := "\nYou are inside the Docker container!\n"
DOCKER_PROJECT_ROOT := /app

PHONY += down
down: ## Tear down the environment
ifeq ($(RUN_ON),host)
	$(call colorecho,$(DOCKER_WARNING_INSIDE))
else
	$(call colorecho, "\nTear down the environment")
	@docker-compose down -v --remove-orphans
endif

PHONY += stop
stop: ## Stop the environment
ifeq ($(RUN_ON),host)
	$(call colorecho,$(DOCKER_WARNING_INSIDE))
else
	$(call colorecho, "\nStop the environment")
	@docker-compose stop
endif

PHONY += up
up: ## Launch the environment
ifeq ($(RUN_ON),host)
	$(call colorecho,$(DOCKER_WARNING_INSIDE))
else
	$(call colorecho, "\nStart up the container(s)\n")
	@docker-compose up -d --remove-orphans
endif

PHONY += docker-test
docker-test: ## Run docker targets on Docker and host
	$(call colorecho, "\nTest docker_run_cmd on $(RUN_ON)")
	$(call docker_run_cmd,pwd && echo \$$PATH)

PHONY += shell
shell: ## Login to CLI container
ifeq ($(RUN_ON),host)
	$(call colorecho,$(DOCKER_WARNING_INSIDE))
else
	@docker-compose exec -u ${CLI_USER} ${CLI_SERVICE} ${CLI_SHELL}
endif

ifeq ($(RUN_ON),docker)
define docker_run_cmd
	@${DOCKER_COMPOSE_EXEC} -u ${CLI_USER} ${CLI_SERVICE} ${CLI_SHELL} -c "$(1)"
endef
else
define docker_run_cmd
	@$(1)
endef
endif
