#!/bin/bash

function getFileName() {
  INPUT_FILE=$(basename -- "$1")
  INPUT_FILE="${INPUT_FILE%.*}"

  echo "${INPUT_FILE}"
}

function getFileExtension() {
  INPUT_FILE=$(basename -- "$1")
  inputExtension="${INPUT_FILE##*.}"

  echo "${inputExtension}"
}

function runQueryOnServer() {
  HOST=$1
  PORT=$2
  DATABASE=$3
  SOURCE_FORMAT=$4

  QUERY=$5

  PATH_WITH_FILE_TO_SAVE=$6

  rm -f "${PATH_WITH_FILE_TO_SAVE}"

  yaz-client -m "${PATH_WITH_FILE_TO_SAVE}" "${HOST}:${PORT}/${DATABASE}" <<EOF
    querytype prefix
    format ${SOURCE_FORMAT}
    f ${QUERY}
    show
    q
EOF
}

function convertUnimarcToUnimarcXml() {
  INPUT_FILE_WITH_PATH=$1
  INPUT_FILE=$(getFileName "${INPUT_FILE_WITH_PATH}")

  OUTPUT_FILE_WITH_PATH=$2

  TEXT_CODE="UTF-8"

  yaz-marcdump -f "${TEXT_CODE}" -t "${TEXT_CODE}" -o "marcxml" "${INPUT_FILE_WITH_PATH}" >"${OUTPUT_FILE_WITH_PATH}"
}

function convertUnimarcXmlToMarc21Xml() {
  TEMPLATE_FILE_WITH_PATH=$1
  INPUT_FILE_WITH_PATH=$2
  OUTPUT_FILE_WITH_PATH=$3

  xsltproc -o "${OUTPUT_FILE_WITH_PATH}" "${TEMPLATE_FILE_WITH_PATH}" "${INPUT_FILE_WITH_PATH}"
}
function convertMarc21XmlToMarc21() {
  INPUT_FILE_WITH_PATH=$1
  OUTPUT_FILE_WITH_PATH=$2

  yaz-marcdump -i "marcxml" -o "marc" "${INPUT_FILE_WITH_PATH}" > "${OUTPUT_FILE_WITH_PATH}"
}

# Server info
HOST=$1
PORT=$2
DATABASE=$3
FORMAT=$4
# Query
QUERY=$5
# Storage info
FILE_TO_SAVE=$6
STORAGE_PATH=$7
INPUT_STORAGE_PATH="${STORAGE_PATH}/input"
OUTPUT_STORAGE_PATH="${STORAGE_PATH}/output"
TEMPLATE_FILE_WITH_PATH=$8

QUERY_RESULT_FILE="${INPUT_STORAGE_PATH}/${FILE_TO_SAVE}"
UNIMARC_XML_FILE="${OUTPUT_STORAGE_PATH}/${FILE_TO_SAVE}.unimarc.xml"
MARC21_XML_FILE="${OUTPUT_STORAGE_PATH}/${FILE_TO_SAVE}.marc21.xml"
MARC21_FILE="${OUTPUT_STORAGE_PATH}/${FILE_TO_SAVE}.marc21.mrc"

runQueryOnServer "${HOST}" "${PORT}" "${DATABASE}" "${FORMAT}" "${QUERY}" "${QUERY_RESULT_FILE}"

if ! [[ -f "${QUERY_RESULT_FILE}" ]]; then
    exit
fi
if ! [ -s "${QUERY_RESULT_FILE}" ]
then
  rm -f "${QUERY_RESULT_FILE}"
	exit
fi
convertUnimarcToUnimarcXml "${QUERY_RESULT_FILE}" "${UNIMARC_XML_FILE}"

if ! [[ -f "${UNIMARC_XML_FILE}" ]]; then
    exit
fi
convertUnimarcXmlToMarc21Xml "${TEMPLATE_FILE_WITH_PATH}" "${UNIMARC_XML_FILE}" "${MARC21_XML_FILE}"

if ! [[ -f "${MARC21_XML_FILE}" ]]; then
    exit
fi
convertMarc21XmlToMarc21 "${MARC21_XML_FILE}" "${MARC21_FILE}"