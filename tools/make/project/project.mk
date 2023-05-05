BUILD_TARGETS += copy-robots

PHONY += copy-robots
copy-robots: ## Copy robots.txt
	$(call step,Copying robots.txt for all environments.\n)
	$(call copy,conf/robots.txt,public/robots.txt)

PHONY += build-prod-image
build-prod-image: ## Test building production Docker image
	docker buildx bake -f docker-bake.hcl --pull --progress plain --no-cache --load && docker rmi suomidigi:prod
