BUILD_TARGETS += copy-robots
#DRUPAL_POST_INSTALL_TARGETS := drush-updb-fix drush-updb drush-cim

PHONY += copy-robots
copy-robots: ## Copy robots.txt
	$(call step,Copying robots.txt for all environments.\n)
	$(call copy,conf/robots.txt,public/robots.txt)

PHONY += drush-updb-fix
drush-updb-fix: ## Run database updates
	$(call step,Run reordered database updates...)
	$(call drush,php-eval 'field_purge_batch(1000);')
	$(call drush,php-eval 'module_load_install("group"); group_update_8017(); group_update_8022();')

PHONY += build-prod-image
build-prod-image: ## Test building production Docker image
	docker buildx bake -f docker-bake.hcl --pull --progress plain --no-cache --load && docker rmi suomidigi:prod

KUBECTL_NAMESPACE ?= foobar-namespace
KUBECTL_EXEC_FLAGS ?= -n $(KUBECTL_NAMESPACE) -c $(KUBECTL_CONTAINER)
KUBECTL_WORKDIR ?= /app
KUBECTL_POD_SELECTOR ?= --field-selector=status.phase==Running

# kubectl.dvv-suomidigi-prod exec suomidigi-drupal-0 -- bash -c 'drush sql-dump --structure-tables-key=common > /tmp/current.sql'
PHONY += kubectl-sync-db
kubectl-sync-db: ## Sync database from Kubernetes
	$(call step,Get database dump from $(POD)...)
	$(call kubectl_exec_to_file,suomidigi-drupal-0,drush sql-dump --structure-tables-key=common --extra-dump=--no-tablespaces,$(DUMP_SQL_FILENAME))

define kubectl_exec_to_file
	kubectl.dvv-suomidigi-prod exec $(1) -- sh -c '$(2)' > $(3)
endef
