import { ChevronDown, ChevronRight, Plus, Edit, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface AnalyticalAccount {
    id: number;
    name: string;
    code: string;
    type: 'nature' | 'cost_center';
    parent_id?: number;
    children?: AnalyticalAccount[];
}

interface FinanceAnalytiqueProps {
    accounts?: AnalyticalAccount[];
}

export default function FinanceAnalytique({
    accounts = [],
}: FinanceAnalytiqueProps) {
    const [expandedNodes, setExpandedNodes] = useState<Set<number>>(new Set());
    const [editingAccount, setEditingAccount] =
        useState<AnalyticalAccount | null>(null);

    const toggleExpanded = (id: number) => {
        const newExpanded = new Set(expandedNodes);

        if (newExpanded.has(id)) {
            newExpanded.delete(id);
        } else {
            newExpanded.add(id);
        }

        setExpandedNodes(newExpanded);
    };

    const addSubAccount = (parentId: number) => {
        setEditingAccount({
            id: 0,
            name: '',
            code: '',
            type: 'cost_center',
            parent_id: parentId,
        });
    };

    const editAccount = (account: AnalyticalAccount) => {
        setEditingAccount(account);
    };

    const saveAccount = () => {
        // Logic to save account via API
        setEditingAccount(null);
    };

    const deleteAccount = (accountId: number) => {
        // Logic to delete account via API
    };

    const renderTreeNode = (account: AnalyticalAccount, level = 0) => {
        const isExpanded = expandedNodes.has(account.id);
        const hasChildren = account.children && account.children.length > 0;

        return (
            <div key={account.id}>
                <div
                    className={`flex items-center border-l-2 px-4 py-2 hover:bg-gray-50 ${
                        level > 0
                            ? 'ml-4 border-l-gray-200'
                            : 'border-l-transparent'
                    }`}
                    style={{ paddingLeft: `${level * 20 + 16}px` }}
                >
                    {hasChildren ? (
                        <Button
                            variant="ghost"
                            size="sm"
                            className="mr-2 h-4 w-4 p-0"
                            onClick={() => toggleExpanded(account.id)}
                        >
                            {isExpanded ? (
                                <ChevronDown className="h-4 w-4" />
                            ) : (
                                <ChevronRight className="h-4 w-4" />
                            )}
                        </Button>
                    ) : (
                        <div className="w-6" />
                    )}

                    <div className="flex-1">
                        <div className="flex items-center justify-between">
                            <div>
                                <span className="font-medium">
                                    {account.name}
                                </span>
                                <span className="ml-2 text-sm text-gray-500">
                                    ({account.code})
                                </span>
                                <span
                                    className={`ml-2 rounded px-2 py-1 text-xs ${
                                        account.type === 'nature'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-green-100 text-green-800'
                                    }`}
                                >
                                    {account.type === 'nature'
                                        ? "Nature d'activité"
                                        : 'Centre de coût'}
                                </span>
                            </div>
                            <div className="flex items-center space-x-2">
                                {account.type === 'nature' && (
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        onClick={() =>
                                            addSubAccount(account.id)
                                        }
                                    >
                                        <Plus className="mr-1 h-4 w-4" />
                                        Sous-compte
                                    </Button>
                                )}
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => editAccount(account)}
                                >
                                    <Edit className="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => deleteAccount(account.id)}
                                >
                                    <Trash2 className="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                {isExpanded && hasChildren && (
                    <div>
                        {account.children!.map((child) =>
                            renderTreeNode(child, level + 1),
                        )}
                    </div>
                )}
            </div>
        );
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <h3 className="text-lg font-medium">Comptes Analytiques</h3>
                <Button
                    onClick={() =>
                        setEditingAccount({
                            id: 0,
                            name: '',
                            code: '',
                            type: 'nature',
                        })
                    }
                >
                    <Plus className="mr-2 h-4 w-4" />
                    Nouvelle Nature d'activité
                </Button>
            </div>

            <Card>
                <CardContent className="p-0">
                    <div className="divide-y divide-gray-200">
                        {accounts.map((account) => renderTreeNode(account))}
                    </div>
                </CardContent>
            </Card>

            {editingAccount && (
                <Card>
                    <CardContent className="p-6">
                        <h4 className="mb-4 text-lg font-medium">
                            {editingAccount.id ? 'Modifier' : 'Ajouter'} un
                            compte analytique
                        </h4>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="account-name">Nom</Label>
                                <Input
                                    id="account-name"
                                    value={editingAccount.name}
                                    onChange={(e) =>
                                        setEditingAccount({
                                            ...editingAccount,
                                            name: e.target.value,
                                        })
                                    }
                                    placeholder="Nom du compte"
                                />
                            </div>
                            <div>
                                <Label htmlFor="account-code">Code</Label>
                                <Input
                                    id="account-code"
                                    value={editingAccount.code}
                                    onChange={(e) =>
                                        setEditingAccount({
                                            ...editingAccount,
                                            code: e.target.value,
                                        })
                                    }
                                    placeholder="Code du compte"
                                />
                            </div>
                        </div>
                        <div className="mt-4 flex justify-end space-x-2">
                            <Button
                                variant="outline"
                                onClick={() => setEditingAccount(null)}
                            >
                                Annuler
                            </Button>
                            <Button onClick={saveAccount}>Enregistrer</Button>
                        </div>
                    </CardContent>
                </Card>
            )}
        </div>
    );
}
