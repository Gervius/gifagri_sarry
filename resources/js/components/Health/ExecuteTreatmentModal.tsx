import { useForm } from '@inertiajs/react';
import { X, Calendar, AlertCircle, Info, Stethoscope, Banknote, FileText, Package, BriefcaseMedical } from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { treatmentsStore } from '@/routes';

interface Batch {
    id: number;
    name: string;
    current_quantity: number;
}

interface ScheduledTreatment {
    id: number;
    flock_name: string;
    step_name: string;
    scheduled_date_formatted: string;
    status: 'pending' | 'missed';
    days_overdue?: number;
}

interface Props {
    isOpen: boolean;
    onClose: () => void;
    scheduledTreatment: ScheduledTreatment | null;
    batches: Batch[];
}

export default function ExecuteTreatmentModal({ isOpen, onClose, scheduledTreatment, batches = [] }: Props) {
    const today = new Date().toISOString().split('T')[0];
    const [medicineOrigin, setMedicineOrigin] = useState<'farm' | 'vet'>('farm');

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        treatment_date: today,
        veterinarian: '',
        cost: '',
        batch_id: '',
        description: '',
    });

    useEffect(() => {
        if (isOpen) {
            reset();
            clearErrors();
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setMedicineOrigin('farm');
        }
    }, [isOpen, reset, clearErrors]);

    if (!isOpen || !scheduledTreatment) {
return null;
}

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const submitData = {
            ...data,
            cost: data.cost ? Number(data.cost) : null,
            batch_id: medicineOrigin === 'farm' && data.batch_id ? Number(data.batch_id) : null,
            scheduled_treatment_id: scheduledTreatment.id,
        };

        post(treatmentsStore(), {
            ...submitData,
            onSuccess: () => {
                onClose();
            },
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm animate-in fade-in duration-200">
            <div className="bg-white rounded-2xl w-full max-w-2xl shadow-xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div className="flex items-center justify-between px-6 py-4 border-b border-stone-200 bg-stone-50">
                    <h2 className="text-xl font-bold text-stone-900">
                        Exécuter : {scheduledTreatment.step_name}
                    </h2>
                    <button
                        onClick={onClose}
                        className="p-2 text-stone-400 hover:text-stone-600 hover:bg-stone-200 rounded-xl transition-colors"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="p-6 md:p-8 space-y-6">
                        <div className="bg-stone-100 rounded-xl p-4 flex flex-col sm:flex-row gap-4 border border-stone-200">
                            <div className="flex items-center gap-2 text-sm text-stone-700">
                                <Info className="w-4 h-4 text-stone-400" />
                                <span>Bande : <strong className="text-stone-900">{scheduledTreatment.flock_name}</strong></span>
                            </div>
                            <div className="flex items-center gap-2 text-sm text-stone-700">
                                <Calendar className="w-4 h-4 text-stone-400" />
                                <span>Date prévue : <strong className="text-stone-900">{scheduledTreatment.scheduled_date_formatted}</strong></span>
                            </div>
                            {scheduledTreatment.status === 'missed' && (
                                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <AlertCircle className="w-3 h-3" />
                                    En retard {scheduledTreatment.days_overdue ? `(${scheduledTreatment.days_overdue}j)` : ''}
                                </span>
                            )}
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label
                                className={`cursor-pointer border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 text-center transition-all ${
                                    medicineOrigin === 'farm'
                                        ? 'border-amber-500 bg-amber-50 shadow-sm shadow-amber-500/10'
                                        : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-500'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="medicineOrigin"
                                    value="farm"
                                    checked={medicineOrigin === 'farm'}
                                    onChange={(e) => setMedicineOrigin(e.target.value as 'farm')}
                                    className="sr-only"
                                />
                                <Package className={`w-8 h-8 ${medicineOrigin === 'farm' ? 'text-amber-500' : 'text-stone-400'}`} />
                                <div>
                                    <h4 className={`font-semibold ${medicineOrigin === 'farm' ? 'text-amber-700' : 'text-stone-700'}`}>Stock de la ferme</h4>
                                    <p className="text-xs mt-1">Utiliser un lot de médicament existant</p>
                                </div>
                            </label>

                            <label
                                className={`cursor-pointer border rounded-2xl p-4 flex flex-col items-center justify-center gap-3 text-center transition-all ${
                                    medicineOrigin === 'vet'
                                        ? 'border-amber-500 bg-amber-50 shadow-sm shadow-amber-500/10'
                                        : 'border-stone-200 bg-white hover:bg-stone-50 text-stone-500'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name="medicineOrigin"
                                    value="vet"
                                    checked={medicineOrigin === 'vet'}
                                    onChange={(e) => setMedicineOrigin(e.target.value as 'vet')}
                                    className="sr-only"
                                />
                                <BriefcaseMedical className={`w-8 h-8 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`} />
                                <div>
                                    <h4 className={`font-semibold ${medicineOrigin === 'vet' ? 'text-amber-700' : 'text-stone-700'}`}>Fourni par le vétérinaire</h4>
                                    <p className="text-xs mt-1">Saisir les informations d'intervention</p>
                                </div>
                            </label>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="flex flex-col gap-2">
                                <label htmlFor="treatment_date" className="text-sm font-medium text-stone-700">Date d'exécution <span className="text-red-500">*</span></label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Calendar className="w-4 h-4 text-stone-400" />
                                    </div>
                                    <input
                                        type="date"
                                        id="treatment_date"
                                        value={data.treatment_date}
                                        max={today}
                                        onChange={(e) => setData('treatment_date', e.target.value)}
                                        required
                                        className="h-12 w-full pl-11 pr-4 rounded-xl border border-stone-200 bg-white text-stone-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-shadow outline-none"
                                    />
                                </div>
                                {errors.treatment_date && <p className="text-sm text-red-600">{errors.treatment_date}</p>}
                            </div>

                            {medicineOrigin === 'farm' && (
                                <div className="flex flex-col gap-2">
                                    <label htmlFor="batch_id" className="text-sm font-medium text-stone-700">Lot de médicament <span className="text-red-500">*</span></label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <Package className="w-4 h-4 text-stone-400" />
                                        </div>
                                        <select
                                            id="batch_id"
                                            value={data.batch_id}
                                            onChange={(e) => setData('batch_id', e.target.value)}
                                            required
                                            className="h-12 w-full pl-11 pr-4 rounded-xl border border-stone-200 bg-white text-stone-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-shadow outline-none appearance-none"
                                        >
                                            <option value="" disabled>Sélectionner un produit...</option>
                                            {batches.map((batch) => (
                                                <option key={batch.id} value={batch.id}>
                                                    {batch.name} ({batch.current_quantity} unités)
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    {errors.batch_id && <p className="text-sm text-red-600">{errors.batch_id}</p>}
                                </div>
                            )}

                            <div className={`flex flex-col gap-2 ${medicineOrigin === 'vet' ? '' : ''}`}>
                                <label htmlFor="veterinarian" className={`text-sm font-medium ${medicineOrigin === 'vet' ? 'text-amber-700 font-bold' : 'text-stone-700'}`}>Vétérinaire {medicineOrigin === 'vet' && <span className="text-red-500">*</span>}</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Stethoscope className={`w-4 h-4 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`} />
                                    </div>
                                    <input
                                        type="text"
                                        id="veterinarian"
                                        value={data.veterinarian}
                                        onChange={(e) => setData('veterinarian', e.target.value)}
                                        required={medicineOrigin === 'vet'}
                                        className={`h-12 w-full pl-11 pr-4 rounded-xl border bg-white text-stone-900 focus:ring-2 transition-shadow outline-none ${medicineOrigin === 'vet' ? 'border-amber-300 focus:ring-amber-500 focus:border-amber-500 shadow-sm shadow-amber-500/10' : 'border-stone-200 focus:ring-amber-500 focus:border-amber-500'}`}
                                        placeholder="Nom du médecin..."
                                    />
                                </div>
                                {errors.veterinarian && <p className="text-sm text-red-600">{errors.veterinarian}</p>}
                            </div>

                            <div className={`flex flex-col gap-2 ${medicineOrigin === 'vet' ? 'sm:col-span-2' : ''}`}>
                                <label htmlFor="cost" className={`text-sm font-medium ${medicineOrigin === 'vet' ? 'text-amber-700 font-bold' : 'text-stone-700'}`}>Coût (Optionnel)</label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <Banknote className={`w-4 h-4 ${medicineOrigin === 'vet' ? 'text-amber-500' : 'text-stone-400'}`} />
                                    </div>
                                    <input
                                        type="number"
                                        id="cost"
                                        value={data.cost}
                                        min="0"
                                        step="0.01"
                                        onChange={(e) => setData('cost', e.target.value)}
                                        className={`h-12 w-full pl-11 pr-4 rounded-xl border bg-white text-stone-900 focus:ring-2 transition-shadow outline-none ${medicineOrigin === 'vet' ? 'border-amber-300 focus:ring-amber-500 focus:border-amber-500 shadow-sm shadow-amber-500/10' : 'border-stone-200 focus:ring-amber-500 focus:border-amber-500'}`}
                                        placeholder="0.00"
                                    />
                                </div>
                                {errors.cost && <p className="text-sm text-red-600">{errors.cost}</p>}
                            </div>

                            <div className="flex flex-col gap-2 sm:col-span-2">
                                <label htmlFor="description" className="text-sm font-medium text-stone-700">Description / Produit utilisé (Optionnel)</label>
                                <div className="relative">
                                    <div className="absolute top-3 left-0 pl-4 pointer-events-none">
                                        <FileText className="w-4 h-4 text-stone-400" />
                                    </div>
                                    <textarea
                                        id="description"
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        rows={3}
                                        className="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-200 bg-white text-stone-900 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-shadow outline-none resize-none"
                                        placeholder={medicineOrigin === 'vet' ? "Notez le nom du médicament fourni par le vétérinaire..." : "Ajoutez des détails sur l'exécution du soin..."}
                                    />
                                </div>
                                {errors.description && <p className="text-sm text-red-600">{errors.description}</p>}
                            </div>
                        </div>
                    </div>

                    <div className="px-6 py-5 border-t border-stone-200 bg-stone-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-5 py-2.5 rounded-xl font-medium text-stone-600 hover:text-stone-900 hover:bg-stone-200 transition-colors"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Confirmer l'exécution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
