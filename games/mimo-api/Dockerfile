FROM node:18-alpine
RUN apk add --no-cache tmux
WORKDIR /app
COPY server.js .
EXPOSE 3456
CMD ["node", "server.js"]
