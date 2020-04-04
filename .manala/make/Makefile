include \
  .manala/make/make.text.mk \
  .manala/make/make.help.mk \
  .manala/make/make.os.mk \
  .manala/make/make.try.mk \
  .manala/make/make.git.mk \
  .manala/make/make.semver.mk

##########
# Docker #
##########

include .manala/make/make.docker.mk

ifneq ($(container),docker)
HELP += $(call help_section,Docker)

HELP += $(call help,docker,Run docker container)
docker:
	$(call docker_run)

HELP += \n
endif

############
# Releases #
############

HELP += $(call help_section,Releases)

HELP += $(call help,release@production,Release in production)
release@production: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
release@production:
		ansible-playbook .manala/ansible/release.yaml \
			--inventory .manala/ansible/release.inventory.yaml \
			--limit production

HELP += $(call help,deploy@production,Deploy in production)
deploy@production: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
deploy@production:
	ansible-playbook .manala/ansible/deploy.yaml \
		--inventory .manala/ansible/deploy.inventory.yaml \
		--limit production

HELP += $(call help,release@staging,Release in staging)
release@staging: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
release@staging:
		ansible-playbook .manala/ansible/release.yaml \
			--inventory .manala/ansible/release.inventory.yaml \
			--limit staging

HELP += $(call help,deploy@staging,Deploy in staging)
deploy@staging: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
deploy@staging:
	ansible-playbook .manala/ansible/deploy.yaml \
		--inventory .manala/ansible/deploy.inventory.yaml \
		--limit staging

HELP += \n

#######
# App #
#######

HELP += $(call help_section,App)
