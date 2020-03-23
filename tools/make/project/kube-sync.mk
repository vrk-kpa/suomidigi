#SYNC_TARGETS := custom-sync
PHONY += kube-sync
kube-sync: ## Sync database and files
	$(call step,Sync database from @$(DRUPAL_SYNC_SOURCE)...)
	## Copy
	kubectl.dvv-suomidigi-beta exec suomidigi-drupal-0 -- bash -c 'mysqldump -h $$DRUPAL_DB_HOST -p$$DRUPAL_DB_PASS -u $$DRUPAL_DB_USER $$DRUPAL_DB_NAME > /tmp/current.sql'
	## Copy current database to local machine
	kubectl.dvv-suomidigi-beta cp suomidigi-drupal-0:/tmp/current.sql files_private/current.sql
	## Import local database
	$(call drush_on_docker, sql-cli < ../files_private/current.sql)
#	joku_muu_tapa_synkata_db
	$(call step,Sync files from @$(DRUPAL_SYNC_SOURCE)...)
#	joku_muu_tapa_synkata_filut
