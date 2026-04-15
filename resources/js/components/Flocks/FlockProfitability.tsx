import { TrendingUp, TrendingDown, Target, Banknote } from 'lucide-react';
import React from 'react';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
    Cell
} from 'recharts';

interface FinancialData {
    costs: { purchase: number; veterinary: number; feed_and_other: number; total: number };
    revenues: { eggs: number; reforms: number; total: number };
    kpis: { gross_margin: number; profitability_index: number; break_even_trays: number; status: 'profitable' | 'amortizing' };
    waterfall_data: { name: string; amount: number; isTotal?: boolean }[];
}

interface Props {
    data: FinancialData;
    features?: { has_eggs: boolean; has_gmq: boolean; is_breeding: boolean };
}

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        maximumFractionDigits: 0,
    }).format(value);
};

const CustomTooltip = ({ active, payload }: any) => {
    if (active && payload && payload.length) {
        const data = payload[0].payload;
        const value = data.originalAmount;

        return (
            <div className="bg-stone-50 border border-stone-200 p-3 rounded-xl shadow-lg text-sm">
                <p className="font-semibold text-stone-900 mb-1">{data.name}</p>
                <p className={value >= 0 ? "text-emerald-600 font-bold" : "text-red-600 font-bold"}>
                    {formatCurrency(value)}
                </p>
            </div>
        );
    }

    return null;
};

export default function FlockProfitability({ data }: Props) {
    const { costs, revenues, kpis, waterfall_data } = data;

    // Use reduce to compute the running total without mutating a variable outside the block
    const chartData = waterfall_data.reduce(
        (acc, item) => {
            if (item.isTotal) {
                acc.data.push({
                    name: item.name,
                    transparent: 0,
                    val: item.amount,
                    fill: item.amount >= 0 ? '#10b981' : '#ef4444',
                    originalAmount: item.amount,
                });
            } else {
                const previousTotal = acc.runningTotal;
                const newTotal = previousTotal + item.amount;
                acc.runningTotal = newTotal;

                acc.data.push({
                    name: item.name,
                    transparent: item.amount < 0 ? newTotal : previousTotal,
                    val: Math.abs(item.amount),
                    fill: item.amount >= 0 ? '#3b82f6' : '#f59e0b',
                    originalAmount: item.amount,
                });
            }

            return acc;
        },
        { data: [] as any[], runningTotal: 0 }
    ).data;

    return (
        <div className="space-y-8">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="p-6 rounded-2xl border bg-stone-50 border-stone-200 flex items-start justify-between">
                    <div>
                        <p className="text-sm font-medium text-stone-500 mb-1">Coûts</p>
                        <h3 className="text-2xl font-bold text-stone-900">
                            {formatCurrency(costs.total)}
                        </h3>
                    </div>
                    <TrendingDown className="w-6 h-6 text-red-500" />
                </div>

                <div className="p-6 rounded-2xl border bg-stone-50 border-stone-200 flex items-start justify-between">
                    <div>
                        <p className="text-sm font-medium text-stone-500 mb-1">Revenus</p>
                        <h3 className="text-2xl font-bold text-stone-900">
                            {formatCurrency(revenues.total)}
                        </h3>
                    </div>
                    <Banknote className="w-6 h-6 text-emerald-500" />
                </div>

                <div className="p-6 rounded-2xl border bg-stone-50 border-stone-200 flex items-start justify-between">
                    <div>
                        <p className="text-sm font-medium text-stone-500 mb-1">Marge Brute</p>
                        <h3 className={`text-2xl font-bold ${kpis.gross_margin >= 0 ? 'text-emerald-600' : 'text-red-600'}`}>
                            {formatCurrency(kpis.gross_margin)}
                        </h3>
                    </div>
                    <TrendingUp className={`w-6 h-6 ${kpis.gross_margin >= 0 ? 'text-emerald-500' : 'text-red-500'}`} />
                </div>

                <div className="p-6 rounded-2xl border bg-stone-50 border-stone-200 flex items-start justify-between">
                    <div>
                        <p className="text-sm font-medium text-stone-500 mb-1">Indice de Rentabilité</p>
                        <h3 className="text-2xl font-bold text-stone-900">
                            {kpis.profitability_index.toFixed(2)}x
                        </h3>
                    </div>
                    <Target className="w-6 h-6 text-amber-500" />
                </div>
            </div>

            <div className="bg-stone-50 border border-stone-200 rounded-2xl p-8">
                <h3 className="font-semibold text-stone-900 mb-8 text-lg">Évolution de la rentabilité</h3>
                <div className="w-full h-[400px]">
                    <ResponsiveContainer width="100%" height="100%">
                        <BarChart data={chartData} margin={{ top: 20, right: 30, left: 20, bottom: 5 }}>
                            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#e7e5e4" />
                            <XAxis dataKey="name" tick={{ fontSize: 12, fill: '#78716c' }} axisLine={false} tickLine={false} />
                            <YAxis tick={{ fontSize: 12, fill: '#78716c' }} axisLine={false} tickLine={false} tickFormatter={(val) => `${val / 1000}k`} />
                            <Tooltip content={<CustomTooltip />} cursor={{ fill: 'transparent' }} />

                            <Bar dataKey="transparent" stackId="a" fill="transparent" />

                            <Bar dataKey="val" stackId="a" radius={[4, 4, 4, 4]}>
                                {chartData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.fill} />
                                ))}
                            </Bar>
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            </div>
        </div>
    );
}
