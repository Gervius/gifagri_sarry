import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Upload, X } from 'lucide-react';

interface CompanySettingsProps {
    company?: {
        name: string;
        address: string;
        phone: string;
        email: string;
        logo?: string;
    };
}

export default function CompanySettings({ company }: CompanySettingsProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: company?.name || '',
        address: company?.address || '',
        phone: company?.phone || '',
        email: company?.email || '',
        logo: null as File | null,
    });

    const [previewUrl, setPreviewUrl] = useState<string | null>(company?.logo || null);

    const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('logo', file);
            const url = URL.createObjectURL(file);
            setPreviewUrl(url);
        }
    };

    const removeLogo = () => {
        setData('logo', null);
        setPreviewUrl(null);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('company-settings.update'));
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                    <div>
                        <Label htmlFor="name">Nom de l'entreprise</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Entrez le nom de l'entreprise"
                        />
                        {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
                    </div>

                    <div>
                        <Label htmlFor="address">Adresse</Label>
                        <Input
                            id="address"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                            placeholder="Adresse complète"
                        />
                        {errors.address && <p className="text-sm text-red-600">{errors.address}</p>}
                    </div>

                    <div>
                        <Label htmlFor="phone">Téléphone</Label>
                        <Input
                            id="phone"
                            type="tel"
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
                            placeholder="+221 XX XXX XX XX"
                        />
                        {errors.phone && <p className="text-sm text-red-600">{errors.phone}</p>}
                    </div>

                    <div>
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="contact@entreprise.com"
                        />
                        {errors.email && <p className="text-sm text-red-600">{errors.email}</p>}
                    </div>
                </div>

                <div className="space-y-4">
                    <div>
                        <Label>Logo de l'entreprise</Label>
                        <Card className="mt-2">
                            <CardContent className="p-6">
                                {previewUrl ? (
                                    <div className="relative">
                                        <img
                                            src={previewUrl}
                                            alt="Logo preview"
                                            className="w-full h-32 object-contain rounded-lg border"
                                        />
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            className="absolute top-2 right-2"
                                            onClick={removeLogo}
                                        >
                                            <X className="h-4 w-4" />
                                        </Button>
                                    </div>
                                ) : (
                                    <div className="text-center">
                                        <Upload className="mx-auto h-12 w-12 text-gray-400" />
                                        <div className="mt-4">
                                            <Label htmlFor="logo-upload" className="cursor-pointer">
                                                <span className="text-sm font-medium text-gray-900">
                                                    Cliquez pour uploader un logo
                                                </span>
                                                <span className="text-sm text-gray-500 block">
                                                    PNG, JPG jusqu'à 2MB
                                                </span>
                                            </Label>
                                            <Input
                                                id="logo-upload"
                                                type="file"
                                                accept="image/*"
                                                onChange={handleLogoChange}
                                                className="hidden"
                                            />
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                        {errors.logo && <p className="text-sm text-red-600">{errors.logo}</p>}
                    </div>
                </div>
            </div>

            <div className="flex justify-end">
                <Button type="submit" disabled={processing}>
                    {processing ? 'Enregistrement...' : 'Enregistrer les modifications'}
                </Button>
            </div>
        </form>
    );
}

function route(arg0: string): string {
    throw new Error('Function not implemented.');
}
