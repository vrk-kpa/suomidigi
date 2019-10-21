include $(DRUIDFI_TOOLS_MAKE_DIR)common.mk
include $(DRUIDFI_TOOLS_MAKE_DIR)docker.mk

ifeq ($(COMPOSER_JSON_EXISTS),yes)
include $(DRUIDFI_TOOLS_MAKE_DIR)composer.mk
endif

include $(DRUIDFI_TOOLS_MAKE_DIR)javascript.mk
include $(DRUIDFI_TOOLS_MAKE_DIR)drupal.mk

ifeq ($(SYSTEM),AMAZEEIO)
include $(DRUIDFI_TOOLS_MAKE_DIR)amazeeio.mk
endif

include $(DRUIDFI_TOOLS_MAKE_DIR)qa.mk
