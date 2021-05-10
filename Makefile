
.PHONY: help

help:
	@grep -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up:  ## Starts all the containers required run the demo
	docker-compose up -d

down: ## Shuts down all the containers and removes their volume
	docker-compose down --remove-orphans

logs: ## Follow logs
	docker-compose logs -f