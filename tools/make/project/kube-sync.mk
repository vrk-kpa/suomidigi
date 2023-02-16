SYNC_TARGETS := kube-sync
DRUPAL_SYNC_SOURCE = AWS-production

PHONY += kube-sync
kube-sync: ## Sync database and files
ifneq ($(DUMP_SQL_EXISTS),yes)
	$(call step,Sync database from @$(DRUPAL_SYNC_SOURCE)...\n)
	@kubectl.dvv-suomidigi-prod exec suomidigi-drupal-0 -- bash -c 'mysqldump -h $$DRUPAL_DB_HOST -p$$DRUPAL_DB_PASS -u $$DRUPAL_DB_USER $$DRUPAL_DB_NAME > /tmp/current.sql && gzip -f /tmp/current.sql'
	@kubectl.dvv-suomidigi-prod cp suomidigi-drupal-0:/tmp/current.sql.gz files_private/current.sql.gz
	@gunzip -f files_private/current.sql.gz
	@mv files_private/current.sql $(DUMP_SQL_FILENAME)
endif
	$(call step,Import local SQL dump...)
	$(call drush,sql-drop -y)
	$(call drush,sql-query --file=${DOCKER_PROJECT_ROOT}/$(DUMP_SQL_FILENAME),SQL dump imported)
