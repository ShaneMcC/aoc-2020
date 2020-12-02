FROM alpine:edge

ADD entrypoint.sh /entrypoint.sh

RUN apk add --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/testing php8 php8-gmp bash && \
    ln -s /usr/bin/php8 /usr/bin/php && \
    chmod +x /entrypoint.sh && \
    chown -R nobody:nobody /entrypoint.sh

USER nobody

CMD /entrypoint.sh
VOLUME /code
