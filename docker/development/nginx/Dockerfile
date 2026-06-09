FROM nginx:1.27-alpine3.21

COPY ./docker/common/nginx/nginx.conf /etc/nginx/nginx.conf

COPY ./ /app/

#HEALTHCHECK --interval=5s --timeout=3s --start-period=1s CMD curl --fail http://127.0.0.1/health || exit 1

WORKDIR /app

RUN mkdir -p /app/runtime/cache
RUN chmod -R 0777 /app/runtime/cache
RUN mkdir -p /log  \
    #&& chown www-data:www-data /log  #chown: unknown user www-data
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
