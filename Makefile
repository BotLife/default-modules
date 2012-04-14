BUILDPATH = build
GITREMOTE = git://github.com/BotLife/BotLife.git
GITREPO   = $(BUILDPATH)/botlife

default:
	mkdir -p $(BUILDPATH)
	rm -rf $(GITREPO)
	git clone $(GITREMOTE) $(GITREPO) --branch develop
	cd $(BUILDPATH)/botlife; \
	  git submodule init;    \
	  git submodule update;   \
	  sed -i 's/library\/default-modules/..\/..\/../g' run
	echo "#!/bin/sh" > run
	echo "$(BUILDPATH)/botlife/run" >> run
