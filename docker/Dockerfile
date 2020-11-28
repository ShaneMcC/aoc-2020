FROM alpine:latest

ADD entrypoint.sh /entrypoint.sh

RUN apk add --no-cache php7 php7-gmp bash && \
    chmod +x /entrypoint.sh && \
    chown -R nobody:nobody /entrypoint.sh

USER nobody

CMD /entrypoint.sh
VOLUME /code
