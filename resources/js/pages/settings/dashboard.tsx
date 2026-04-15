import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import CompanySettings from './components/CompanySettings';
import FinanceAnalytique from './components/FinanceAnalytique';
import SecurityPermissions from './components/SecurityPermissions';
import ZootechnieSante from './components/ZootechnieSante';

export default function SettingsDashboard() {
    const [activeTab, setActiveTab] = useState('company');

    return (
        <>
            <Head title="Hub de Paramétrage" />

            <div className="container mx-auto px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Hub de Paramétrage</h1>
                    <p className="text-gray-600 mt-2">
                        Centre de contrôle de l'ERP - Gérez tous les paramètres de votre entreprise
                    </p>
                </div>

                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-full grid-cols-4">
                        <TabsTrigger value="company">Entreprise & Infrastructures</TabsTrigger>
                        <TabsTrigger value="zootechnie">Zootechnie & Santé</TabsTrigger>
                        <TabsTrigger value="finance">Finance & Analytique</TabsTrigger>
                        <TabsTrigger value="security">Sécurité</TabsTrigger>
                    </TabsList>

                    <TabsContent value="company" className="mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Paramètres Entreprise</CardTitle>
                                <CardDescription>
                                    Configurez les informations générales de votre entreprise et ses infrastructures
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <CompanySettings />
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="zootechnie" className="mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Zootechnie & Santé</CardTitle>
                                <CardDescription>
                                    Gérez les plans prophylactiques et les protocoles de santé animale
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ZootechnieSante />
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="finance" className="mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Finance & Analytique</CardTitle>
                                <CardDescription>
                                    Structurez vos comptes analytiques et centres de coût
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <FinanceAnalytique />
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="security" className="mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Sécurité & Permissions</CardTitle>
                                <CardDescription>
                                    Gérez les rôles et permissions des utilisateurs
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <SecurityPermissions />
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </>
    );
}