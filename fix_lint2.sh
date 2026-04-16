#!/bin/bash
sed -i 's/const financial_analysis = props.financial_analysis;/const financial_analysis = props.financial_analysis as any;/' resources/js/pages/Flocks/Show.tsx
