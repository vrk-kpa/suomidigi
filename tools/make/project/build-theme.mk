PHONY += build-theme
build-theme: ## Build theme
	$(call step,Build theme...\n)
	$(call node_run,run gulp development)

#PHONY += build-theme
#build-theme: DOCKER_NODE_IMG ?= node:16-alpine
#build-theme: WD := /usr/src/app
#build-theme: ## Install NPM packages and build theme
#	$(call step,Build theme...\n)
#	@docker run --rm -v "$(CURDIR)":$(WD) -w $(WD) $(DOCKER_NODE_IMG) npm run --prefix ./public/themes/custom/suomidigi gulp development
