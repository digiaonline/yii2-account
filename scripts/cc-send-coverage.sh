#!/bin/bash

mv tests/codeception/_output/coverage.xml build/logs/clover.xml
CODECLIMATE_REPO_TOKEN=848604f9639e4a3fcffe8a7485db17a8746ae724ecb5873b0ec571ad37267506 /vagrant/vendor/bin/test-reporter
