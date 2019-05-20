PHONY += test
test: ## Run tests
	$(call colorecho, "\nRun tests:")
	$(call colorecho, "- Following test targets will be run: $(TEST_TARGETS)")
	@$(MAKE) $(TEST_TARGETS)
	$(call colorecho, "\nTests completed.")

PHONY += test-foobar
test-foobar:
	$(call test_result,test-foobar,"[OK]")

define test_result
	@echo "- ${YELLOW}${1}:${NO_COLOR} ${GREEN}${2}${NO_COLOR}"
endef
