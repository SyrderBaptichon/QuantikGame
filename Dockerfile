FROM ubuntu:latest
LABEL authors="syrder"

ENTRYPOINT ["top", "-b"]