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
HELP += $(call help_section, Docker)

HELP += $(call help,docker,Run docker container)
docker:
	$(call docker_run)

endif

############
# Releases #
############

HELP += $(call help_section, Releases)

HELP += $(call help,release@production,Release in production)
release@production: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
release@production:
		ansible-playbook .manala/ansible/release.yaml \
			--inventory .manala/ansible/release.inventory.yaml \
			--limit production

HELP += $(call help,deploy@production,Deploy in production (REF))
deploy@production: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
deploy@production:
	ansible-playbook .manala/ansible/deploy.yaml \
		--inventory .manala/ansible/deploy.inventory.yaml \
		--limit production \
		$(if $(REF),--extra-vars '{"deploy_strategy_git_ref": "$(REF)"}')

HELP += $(call help,release@staging,Release in staging)
release@staging: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
release@staging:
		ansible-playbook .manala/ansible/release.yaml \
			--inventory .manala/ansible/release.inventory.yaml \
			--limit staging

HELP += $(call help,deploy@staging,Deploy in staging (REF))
deploy@staging: SHELL := $(or $(DOCKER_SHELL),$(SHELL))
deploy@staging:
	ansible-playbook .manala/ansible/deploy.yaml \
		--inventory .manala/ansible/deploy.inventory.yaml \
		--limit staging \
		$(if $(REF),--extra-vars '{"deploy_strategy_git_ref": "$(REF)"}')

#######
# App #
#######

HELP += $(call help_section, App)
