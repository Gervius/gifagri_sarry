#!/bin/bash
sed -i 's/setMedicineOrigin('\''farm'\'');/\/\/ eslint-disable-next-line react-hooks\/set-state-in-effect\n            setMedicineOrigin('\''farm'\'');/g' resources/js/components/Health/ExecuteTreatmentModal.tsx
