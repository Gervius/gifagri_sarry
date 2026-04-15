import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Plus, Edit, Trash2 } from 'lucide-react';

interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions: Permission[];
}

interface Permission {
    id: number;
    name: string;
    domain: string;
}

interface SecurityPermissionsProps {
    roles?: Role[];
    allPermissions?: Permission[];
}

export default function SecurityPermissions({ roles = [], allPermissions = [] }: SecurityPermissionsProps) {
    const [selectedRole, setSelectedRole] = useState<Role | null>(null);
    const [rolePermissions, setRolePermissions] = useState<Record<number, boolean>>({});

    const handleRoleSelect = (role: Role) => {
        setSelectedRole(role);
        const permissionsMap: Record<number, boolean> = {};
        role.permissions.forEach((perm) => {
            permissionsMap[perm.id] = true;
        });
        setRolePermissions(permissionsMap);
    };

    const handlePermissionChange = (permissionId: number, checked: boolean) => {
        setRolePermissions((prev) => ({
            ...prev,
            [permissionId]: checked,
        }));
    };

    const savePermissions = () => {
        // Logic to save permissions via API
        console.log('Saving permissions for role:', selectedRole?.id, rolePermissions);
    };

    const groupedPermissions = allPermissions.reduce((acc, perm) => {
        if (!acc[perm.domain]) {
            acc[perm.domain] = [];
        }
        acc[perm.domain].push(perm);
        return acc;
    }, {} as Record<string, Permission[]>);

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h3 className="text-lg font-medium">Gestion des Rôles et Permissions</h3>
                <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Nouveau Rôle
                </Button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Rôles</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                {roles.map((role) => (
                                    <div
                                        key={role.id}
                                        className={`p-3 rounded-lg border cursor-pointer transition-colors ${
                                            selectedRole?.id === role.id
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 hover:border-gray-300'
                                        }`}
                                        onClick={() => handleRoleSelect(role)}
                                    >
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h4 className="font-medium">{role.name}</h4>
                                                <p className="text-sm text-gray-500">
                                                    {role.permissions.length} permissions
                                                </p>
                                            </div>
                                            <div className="flex space-x-1">
                                                <Button variant="ghost" size="sm">
                                                    <Edit className="h-4 w-4" />
                                                </Button>
                                                <Button variant="ghost" size="sm">
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="lg:col-span-2">
                    {selectedRole ? (
                        <Card>
                            <CardHeader>
                                <CardTitle>
                                    Permissions pour le rôle: {selectedRole.name}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-6">
                                    {Object.entries(groupedPermissions).map(([domain, permissions]) => (
                                        <div key={domain}>
                                            <h4 className="font-medium text-lg mb-3 capitalize">
                                                {domain}
                                            </h4>
                                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                {permissions.map((permission) => (
                                                    <div
                                                        key={permission.id}
                                                        className="flex items-center space-x-2"
                                                    >
                                                        <Checkbox
                                                            id={`perm-${permission.id}`}
                                                            checked={rolePermissions[permission.id] || false}
                                                            onCheckedChange={(checked) =>
                                                                handlePermissionChange(
                                                                    permission.id,
                                                                    checked as boolean
                                                                )
                                                            }
                                                        />
                                                        <label
                                                            htmlFor={`perm-${permission.id}`}
                                                            className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            {permission.name.split('_').pop()?.toLowerCase()}
                                                        </label>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="flex justify-end mt-6">
                                    <Button onClick={savePermissions}>
                                        Enregistrer les permissions
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        <Card>
                            <CardContent className="p-12 text-center">
                                <p className="text-gray-500">
                                    Sélectionnez un rôle pour gérer ses permissions
                                </p>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </div>
    );
}