import sys

dim = sys.argv[1]

print("Matrix dimension passed: {", dim, "}")

dim = int(dim)

import os
os.environ['AZURE_CLIENT_ID'] = 'aaaaaaaa-aaaaaaaa-aaaaaaaa-aaaaaaaa-aaaaaaaa'
os.environ['AZURE_CLIENT_SECRET'] = 'bbbbbbbb~bbbbbbbb-bbbbbbbb'
os.environ['AZURE_TENANT_ID'] = 'cccccccc-cccccccc-cccccccc-cccccccc-cccccccc'

from azure.identity import DefaultAzureCredential
from azure.mgmt.resource.resources import ResourceManagementClient

credential = DefaultAzureCredential()

client = ResourceManagementClient(
    credential=credential,
    subscription_id="dddddddd-dddddddd-dddddddd-dddddddd-dddddddd"
)

for resource_group in client.resource_groups.list():
    print(f"Resource group: {resource_group.name}")

print(f"Successful credential: {credential._successful_credential.__class__.__name__}")

from azure.quantum import Workspace 
from qiskit import QuantumCircuit
from azure.quantum.qiskit import AzureQuantumProvider

workspace = Workspace(  
    resource_id = "/subscriptions/dddddddd-dddddddd-dddddddd-dddddddd-dddddddd/resourceGroups/slukyanc-resource-group/providers/Microsoft.Quantum/Workspaces/slukyanc-qws-jbpm", # Add the resourceID of your workspace
    location = "West Europe" # Add the location of your workspace (for example "westus")
    )

provider = AzureQuantumProvider(workspace)

print("This workspace's targets:")
for backend in provider.backends():
    print("- " + backend.name())

qc = QuantumCircuit(dim)
for qb in range(dim):
    qc.h(qb)  # put qubit into superposition
qc.measure_all()

# Get Quantinuum's QPU backend:
qpu_backend = provider.get_backend("rigetti.sim.qvm")

# Submit the circuit to run on Azure Quantum
job = qpu_backend.run(qc, shots=10000)
#job_id = job.id()

# Get the job results (this method waits for the Job to complete):
result = job.result()

total = sum(result.get_counts(qc).values(), 0)
probs = {k: v / total for k, v in result.get_counts(qc).items()}  # {'0': 0.5, '1': 0.5}

import pandas as pd
import numpy as np
import math

Probs=pd.DataFrame.from_dict(probs, orient='index', columns=['Probability'])
Probs['Statevector']=Probs.index
matrix=np.zeros(shape=(int(math.sqrt(dim)),int(math.sqrt(dim))))

for i in range(int(math.sqrt(dim))):
    for j in range(i, int(math.sqrt(dim))):
        matrix[i][j]=Probs['Probability'].where(Probs['Statevector'].str.slice(int(math.sqrt(dim))*i+j,int(math.sqrt(dim))*i+j+1)=='1').sum()
        matrix[j][i]=matrix[i][j]

np.fill_diagonal(matrix, 1)

print(matrix)