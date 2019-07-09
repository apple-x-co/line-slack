.PHONY: install
install:
	cd public
	cp -ap .env.sample .env
	composer install