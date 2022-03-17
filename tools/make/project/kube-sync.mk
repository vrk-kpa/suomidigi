SYNC_TARGETS := kube-sync
PHONY += kube-sync
DRUPAL_SYNC_SOURCE = AWS-production

kube-sync: ## Sync database and files
	$(call step,Sync database from @$(DRUPAL_SYNC_SOURCE)...\n)
## Copy
	kubectl.dvv-suomidigi-prod exec suomidigi-drupal-0 -- bash -c 'mysqldump -h $$DRUPAL_DB_HOST -p$$DRUPAL_DB_PASS -u $$DRUPAL_DB_USER $$DRUPAL_DB_NAME > /tmp/current.sql && gzip -f /tmp/current.sql'
## Copy current database to local machine
	kubectl.dvv-suomidigi-prod cp suomidigi-drupal-0:/tmp/current.sql.gz files_private/current.sql.gz
	gunzip -f files_private/current.sql.gz
## Import local database
	$(call step,Importing database...\n)
	$(call drush,sql-cli < ../files_private/current.sql)
## Don't sync files, since stage file proxy
