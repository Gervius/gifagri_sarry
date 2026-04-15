import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Plus, Edit, Trash2 } from 'lucide-react';

interface ProphylaxisPlan {
    id: number;
    name: string;
    description: string;
    species: string;
    steps: ProphylaxisStep[];
}

interface ProphylaxisStep {
    id: number;
    day_age: number;
    vaccine: string;
    method: string;
}

interface ZootechnieSanteProps {
    plans?: ProphylaxisPlan[];
}

export default function ZootechnieSante({ plans = [] }: ZootechnieSanteProps) {
    const [selectedPlan, setSelectedPlan] = useState<ProphylaxisPlan | null>(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [editingStep, setEditingStep] = useState<ProphylaxisStep | null>(null);

    const handlePlanClick = (plan: ProphylaxisPlan) => {
        setSelectedPlan(plan);
        setIsDialogOpen(true);
    };

    const addStep = () => {
        setEditingStep({ id: 0, day_age: 0, vaccine: '', method: '' });
    };

    const editStep = (step: ProphylaxisStep) => {
        setEditingStep(step);
    };

    const saveStep = () => {
        // Logic to save step via API
        setEditingStep(null);
    };

    const deleteStep = (stepId: number) => {
        // Logic to delete step via API
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h3 className="text-lg font-medium">Plans Prophylactiques</h3>
                <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Nouveau Plan
                </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nom du Plan</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Espèce</TableHead>
                        <TableHead>Nombre d'étapes</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {plans.map((plan) => (
                        <TableRow
                            key={plan.id}
                            className="cursor-pointer hover:bg-gray-50"
                            onClick={() => handlePlanClick(plan)}
                        >
                            <TableCell className="font-medium">{plan.name}</TableCell>
                            <TableCell>{plan.description}</TableCell>
                            <TableCell>{plan.species}</TableCell>
                            <TableCell>{plan.steps.length}</TableCell>
                            <TableCell>
                                <div className="flex space-x-2">
                                    <Button variant="ghost" size="sm">
                                        <Edit className="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogContent className="max-w-4xl">
                    <DialogHeader>
                        <DialogTitle>
                            Étapes du Plan: {selectedPlan?.name}
                        </DialogTitle>
                        <DialogDescription>
                            Gérez les étapes prophylactiques pour ce plan
                        </DialogDescription>
                    </DialogHeader>

                    <div className="space-y-4">
                        <div className="flex justify-between items-center">
                            <h4 className="text-md font-medium">Étapes</h4>
                            <Button onClick={addStep}>
                                <Plus className="h-4 w-4 mr-2" />
                                Ajouter une étape
                            </Button>
                        </div>

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Jour d'âge</TableHead>
                                    <TableHead>Vaccin</TableHead>
                                    <TableHead>Méthode</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {selectedPlan?.steps.map((step) => (
                                    <TableRow key={step.id}>
                                        <TableCell>{step.day_age}</TableCell>
                                        <TableCell>{step.vaccine}</TableCell>
                                        <TableCell>{step.method}</TableCell>
                                        <TableCell>
                                            <div className="flex space-x-2">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => editStep(step)}
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => deleteStep(step.id)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        {editingStep && (
                            <div className="border rounded-lg p-4 space-y-4">
                                <h5 className="font-medium">
                                    {editingStep.id ? 'Modifier' : 'Ajouter'} une étape
                                </h5>
                                <div className="grid grid-cols-3 gap-4">
                                    <div>
                                        <Label htmlFor="day_age">Jour d'âge</Label>
                                        <Input
                                            id="day_age"
                                            type="number"
                                            value={editingStep.day_age}
                                            onChange={(e) =>
                                                setEditingStep({
                                                    ...editingStep,
                                                    day_age: parseInt(e.target.value),
                                                })
                                            }
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="vaccine">Vaccin</Label>
                                        <Input
                                            id="vaccine"
                                            value={editingStep.vaccine}
                                            onChange={(e) =>
                                                setEditingStep({
                                                    ...editingStep,
                                                    vaccine: e.target.value,
                                                })
                                            }
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="method">Méthode</Label>
                                        <Select
                                            value={editingStep.method}
                                            onValueChange={(value) =>
                                                setEditingStep({
                                                    ...editingStep,
                                                    method: value,
                                                })
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Sélectionner une méthode" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="injection">Injection</SelectItem>
                                                <SelectItem value="oral">Oral</SelectItem>
                                                <SelectItem value="spray">Spray</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                                <div className="flex justify-end space-x-2">
                                    <Button
                                        variant="outline"
                                        onClick={() => setEditingStep(null)}
                                    >
                                        Annuler
                                    </Button>
                                    <Button onClick={saveStep}>Enregistrer</Button>
                                </div>
                            </div>
                        )}
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}