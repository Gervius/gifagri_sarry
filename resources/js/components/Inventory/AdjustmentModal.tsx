import { useForm } from '@inertiajs/react';
import { X, Package, Info, FileText } from 'lucide-react';
import React, { useEffect } from 'react';
import { inventoryAdjustmentsStore } from '@/routes';

interface Item {
    id: number;
    name: string;
    current_stock: number;
    type: 'ingredient' | 'feed_stock';
    unit: string;
}

interface Props {
    isOpen: boolean;
    onClose: () => void;
    item: Item | null;
}

export default function AdjustmentModal({ isOpen, onClose, item }: Props) {
    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            stockable_type: '',
            stockable_id: '',
            expected_quantity: 0,
            actual_quantity: '',
            reason: '',
        });

    useEffect(() => {
        if (isOpen && item) {
            clearErrors();
            setData({
                stockable_type:
                    item.type === 'ingredient'
                        ? 'App\\Models\\Ingredient'
                        : 'App\\Models\\FeedStock',
                stockable_id: String(item.id),
                expected_quantity: item.current_stock,
                actual_quantity: '',
                reason: '',
            });
        }
    }, [isOpen, item, clearErrors, setData]);

    if (!isOpen || !item) {
return null;
}

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        post(inventoryAdjustmentsStore(), {
            onSuccess: () => {
                reset();
                onClose();
            },
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex animate-in items-center justify-center bg-stone-900/50 p-4 backdrop-blur-sm duration-200 fade-in">
            <div className="w-full max-w-lg animate-in overflow-hidden rounded-2xl bg-white shadow-xl duration-200 zoom-in-95">
                {/* Header */}
                <div className="flex items-center justify-between border-b border-stone-200 bg-stone-50 px-6 py-4">
                    <h2 className="text-xl font-bold text-stone-900">
                        Ajustement d'inventaire
                    </h2>
                    <button
                        onClick={onClose}
                        className="rounded-xl p-2 text-stone-400 transition-colors hover:bg-stone-200 hover:text-stone-600"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-6 p-6 md:p-8">
                        {/* Info Block */}
                        <div className="flex flex-col gap-2 rounded-xl border border-stone-200 bg-stone-100 p-5">
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg border border-stone-200 bg-white p-2 shadow-sm">
                                    <Package className="h-5 w-5 text-stone-500" />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-stone-500">
                                        Article concerné
                                    </p>
                                    <h4 className="text-lg font-bold text-stone-900">
                                        {item.name}
                                    </h4>
                                </div>
                            </div>
                            <div className="mt-2 flex inline-flex w-fit items-center gap-2 rounded-lg bg-white/50 p-2 text-sm text-stone-600">
                                <Info className="h-4 w-4 text-stone-400" />
                                Quantité théorique attendue :{' '}
                                <strong className="text-stone-900">
                                    {item.current_stock.toLocaleString('fr-FR')}{' '}
                                    {item.unit}
                                </strong>
                            </div>
                        </div>

                        {/* Actual Quantity Input */}
                        <div className="flex flex-col gap-2">
                            <label
                                htmlFor="actual_quantity"
                                className="text-sm font-medium text-stone-700"
                            >
                                Quantité physique réelle{' '}
                                <span className="text-red-500">*</span>
                            </label>
                            <div className="relative flex items-center">
                                <input
                                    type="number"
                                    id="actual_quantity"
                                    value={data.actual_quantity}
                                    onChange={(e) =>
                                        setData(
                                            'actual_quantity',
                                            e.target.value,
                                        )
                                    }
                                    required
                                    min="0"
                                    step="0.01"
                                    className="h-16 w-full rounded-xl border border-stone-200 bg-white pr-16 pl-4 text-xl font-bold text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                    placeholder="0"
                                />
                                <div className="absolute right-4 font-medium text-stone-400">
                                    {item.unit}
                                </div>
                            </div>
                            {errors.actual_quantity && (
                                <p className="text-sm text-red-600">
                                    {errors.actual_quantity}
                                </p>
                            )}
                        </div>

                        {/* Reason Textarea */}
                        <div className="flex flex-col gap-2">
                            <label
                                htmlFor="reason"
                                className="text-sm font-medium text-stone-700"
                            >
                                Motif de l'écart{' '}
                                <span className="text-red-500">*</span>
                            </label>
                            <div className="relative">
                                <div className="pointer-events-none absolute top-3 left-0 pl-4">
                                    <FileText className="h-4 w-4 text-stone-400" />
                                </div>
                                <textarea
                                    id="reason"
                                    value={data.reason}
                                    onChange={(e) =>
                                        setData('reason', e.target.value)
                                    }
                                    required
                                    rows={3}
                                    className="w-full resize-none rounded-xl border border-stone-200 bg-white py-3 pr-4 pl-11 text-stone-900 transition-shadow outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                                    placeholder="Ex: Sacs endommagés par l'humidité, erreur de comptage..."
                                />
                            </div>
                            {errors.reason && (
                                <p className="text-sm text-red-600">
                                    {errors.reason}
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="flex items-center justify-end gap-3 rounded-b-2xl border-t border-stone-200 bg-stone-50 px-6 py-5">
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-xl px-5 py-2.5 font-medium text-stone-600 transition-colors hover:bg-stone-200 hover:text-stone-900"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-xl bg-amber-500 px-5 py-2.5 font-medium text-white shadow-sm transition-colors hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Soumettre l'ajustement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
