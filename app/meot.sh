#!/bin/bash

tmux split-window -h -p 30 'php54 app/console server:run'
tmux split-window -v -p 50 'tail -f app/logs/dev.log'
tmux select-pane -L
