
const placemarks = [
  {
    "name": "ABATE TITTA",
    "latitude": 38.5866683865009,
    "longitude": 15.914711610027
  },
  {
    "name": "ABITUSO",
    "latitude": 38.7562362077523,
    "longitude": 16.3565334611271
  },
  {
    "name": "ACONA",
    "latitude": 38.6121773861984,
    "longitude": 16.209858336683
  },
  {
    "name": "ACONI",
    "latitude": 38.5964385776708,
    "longitude": 16.2598317555029
  },
  {
    "name": "ACQUA FREDDA",
    "latitude": 38.6139258055896,
    "longitude": 15.9035027890445
  },
  {
    "name": "ACQUA RUGIADA",
    "latitude": 38.4502288102536,
    "longitude": 16.2703605131317
  },
  {
    "name": "ACQUABIANCA",
    "latitude": 38.5366101652374,
    "longitude": 16.1972212380793
  },
  {
    "name": "ACQUACALDA",
    "latitude": 38.6341768911763,
    "longitude": 16.1625143955697
  },
  {
    "name": "ACQUAFREDDA",
    "latitude": 38.6199760316269,
    "longitude": 16.2822748032002
  },
  {
    "name": "ACQUANGI",
    "latitude": 38.780493479688,
    "longitude": 16.3212128433733
  },
  {
    "name": "ACQUARO",
    "latitude": 38.5556853538957,
    "longitude": 16.1903877675965
  },
  {
    "name": "ACQUE BIANCHE",
    "latitude": 38.4888753600787,
    "longitude": 16.3387210445792
  },
  {
    "name": "ACQUE MURATE",
    "latitude": 38.4904177345208,
    "longitude": 16.3519354079112
  },
  {
    "name": "AEROPORTO DI VIBO VALENTIA \"LUIGI RAZZA\"",
    "latitude": 38.6383747274778,
    "longitude": 16.0442037179255
  },
  {
    "name": "AGAZZI",
    "latitude": 38.7869089530209,
    "longitude": 16.3363100822808
  },
  {
    "name": "AGRELLI",
    "latitude": 38.4343028356583,
    "longitude": 16.3511172921141
  },
  {
    "name": "AGROMOLARA",
    "latitude": 38.4620911040829,
    "longitude": 16.3372973539911
  },
  {
    "name": "AGUGLIA",
    "latitude": 38.6439674138761,
    "longitude": 16.3007767463309
  },
  {
    "name": "AGUGLIA",
    "latitude": 38.6639030387391,
    "longitude": 16.3198604470482
  },
  {
    "name": "AIA DELLE CHIUSELLE",
    "latitude": 38.7337702336877,
    "longitude": 16.2708348741089
  },
  {
    "name": "AIA DI TUCCIO",
    "latitude": 38.7278834971673,
    "longitude": 16.2718809563885
  },
  {
    "name": "ALAFITA",
    "latitude": 38.6708240053627,
    "longitude": 15.9187242841589
  },
  {
    "name": "ALBANI",
    "latitude": 38.4523547656644,
    "longitude": 16.3494140669684
  },
  {
    "name": "ALENCI",
    "latitude": 38.6272075484962,
    "longitude": 16.2485491236373
  },
  {
    "name": "ALTIPIANO DEL PORO",
    "latitude": 38.6051349942103,
    "longitude": 15.9812576912914
  },
  {
    "name": "AMATO",
    "latitude": 38.5933663897566,
    "longitude": 16.0273838672323
  },
  {
    "name": "AMPINO",
    "latitude": 38.5598485769831,
    "longitude": 16.0914774124843
  },
  {
    "name": "ANGELELLA",
    "latitude": 38.4730124155001,
    "longitude": 16.2883219438648
  },
  {
    "name": "ANGIOLELLA",
    "latitude": 38.6644505756437,
    "longitude": 16.3437888122127
  },
  {
    "name": "ANGITOLELLA",
    "latitude": 38.7225438839239,
    "longitude": 16.3353518492053
  },
  {
    "name": "ANGRI",
    "latitude": 38.6709727047696,
    "longitude": 16.2772298301561
  },
  {
    "name": "ANICITO",
    "latitude": 38.6403058192343,
    "longitude": 15.979301213089
  },
  {
    "name": "ANNIBALE",
    "latitude": 38.5402843923327,
    "longitude": 16.2483642775516
  },
  {
    "name": "ARAMONI",
    "latitude": 38.6263796938769,
    "longitude": 15.9468422145583
  },
  {
    "name": "ARCIDIACONO",
    "latitude": 38.7688753880599,
    "longitude": 16.2047311532929
  },
  {
    "name": "ARCO",
    "latitude": 38.6950497854866,
    "longitude": 16.2413682629315
  },
  {
    "name": "ARCOLEO",
    "latitude": 38.5938977100953,
    "longitude": 16.1766998932816
  },
  {
    "name": "ARENA",
    "latitude": 38.5627961612481,
    "longitude": 16.2096611432952
  },
  {
    "name": "ARENELLA",
    "latitude": 38.4913931753746,
    "longitude": 16.2654531986126
  },
  {
    "name": "ARIOLA",
    "latitude": 38.5685298200635,
    "longitude": 16.2556481493505
  },
  {
    "name": "ARMO",
    "latitude": 38.6634676817531,
    "longitude": 16.1438853735897
  },
  {
    "name": "ARRUGGIATO",
    "latitude": 38.5114374106666,
    "longitude": 16.2585439174647
  },
  {
    "name": "ARTESE",
    "latitude": 38.6099000466652,
    "longitude": 15.9170853681748
  },
  {
    "name": "ARVO",
    "latitude": 38.5945633100166,
    "longitude": 16.3417842756442
  },
  {
    "name": "ARZONA",
    "latitude": 38.621300167945,
    "longitude": 16.0439330867849
  },
  {
    "name": "ASCUTO",
    "latitude": 38.5977064682796,
    "longitude": 16.2398388997186
  },
  {
    "name": "ASMONIO",
    "latitude": 38.6072007625552,
    "longitude": 16.1447026119365
  },
  {
    "name": "ATTESIA",
    "latitude": 38.5594373405507,
    "longitude": 16.2739793318534
  },
  {
    "name": "AZIENDA BRACHO",
    "latitude": 38.53110075566,
    "longitude": 15.9729660593755
  },
  {
    "name": "B. DI PRATTARI",
    "latitude": 38.71913443462,
    "longitude": 16.2659861089676
  },
  {
    "name": "B. DOMITO",
    "latitude": 38.4600873392393,
    "longitude": 16.3772367093698
  },
  {
    "name": "B.NE DELLE TINE",
    "latitude": 38.5862802225072,
    "longitude": 16.3699196668551
  },
  {
    "name": "B.NE ZINZOLO",
    "latitude": 38.7034003644512,
    "longitude": 15.9744456398541
  },
  {
    "name": "BACOLOPANE",
    "latitude": 38.8049656232205,
    "longitude": 16.2858722616407
  },
  {
    "name": "BADIA",
    "latitude": 38.5649890564753,
    "longitude": 15.9571698450359
  },
  {
    "name": "BADILE",
    "latitude": 38.610497961778,
    "longitude": 16.197035646559
  },
  {
    "name": "BAGNERIA",
    "latitude": 38.6593696243968,
    "longitude": 15.8607554732033
  },
  {
    "name": "BANDIERA",
    "latitude": 38.6091733432723,
    "longitude": 16.3032950396855
  },
  {
    "name": "BANDINO",
    "latitude": 38.6097299323561,
    "longitude": 15.9984217900078
  },
  {
    "name": "BARBALACONI",
    "latitude": 38.6346438385023,
    "longitude": 15.8726153656394
  },
  {
    "name": "BARILLA",
    "latitude": 38.5647474354158,
    "longitude": 15.9722718008617
  },
  {
    "name": "BARONE",
    "latitude": 38.6442198567675,
    "longitude": 16.2308156858006
  },
  {
    "name": "BARONIA",
    "latitude": 38.605976758179,
    "longitude": 15.8918494855337
  },
  {
    "name": "BARONIA",
    "latitude": 38.7139001357443,
    "longitude": 16.0740677871148
  },
  {
    "name": "BATIA",
    "latitude": 38.6924908379702,
    "longitude": 16.3074847258271
  },
  {
    "name": "BATTIFOGLIO",
    "latitude": 38.6634765700033,
    "longitude": 16.1073815116934
  },
  {
    "name": "BAUSA",
    "latitude": 38.6968683444681,
    "longitude": 15.9988502119991
  },
  {
    "name": "BELLARDINA",
    "latitude": 38.5065076025018,
    "longitude": 16.2408538243512
  },
  {
    "name": "BELLINO",
    "latitude": 38.5607648961889,
    "longitude": 16.3367483365402
  },
  {
    "name": "BELUSCIA",
    "latitude": 38.6598768655074,
    "longitude": 15.8542563174488
  },
  {
    "name": "BENEFICIO",
    "latitude": 38.6375379138149,
    "longitude": 16.1106014612885
  },
  {
    "name": "BERIGLIANA",
    "latitude": 38.4798088735945,
    "longitude": 16.2898071801311
  },
  {
    "name": "BETTARELLA",
    "latitude": 38.6933759175886,
    "longitude": 16.0468081864305
  },
  {
    "name": "BEVILACQUA",
    "latitude": 38.8212954321479,
    "longitude": 16.2947014415961
  },
  {
    "name": "BIVIO CESSANITI",
    "latitude": 38.6436390643182,
    "longitude": 16.0440273234696
  },
  {
    "name": "BIVIO IANNI",
    "latitude": 38.5213391979949,
    "longitude": 15.9946298440102
  },
  {
    "name": "BIVIO S. MARCO",
    "latitude": 38.6672390599916,
    "longitude": 16.0175880655399
  },
  {
    "name": "BIVONA",
    "latitude": 38.7101207867918,
    "longitude": 16.1015681152676
  },
  {
    "name": "BOCCA D'ASSI",
    "latitude": 38.5717682911422,
    "longitude": 16.403678680012
  },
  {
    "name": "BOMBULERO",
    "latitude": 38.5686874974989,
    "longitude": 16.1444700249085
  },
  {
    "name": "BONDANZA",
    "latitude": 38.6160657138136,
    "longitude": 16.2429689884585
  },
  {
    "name": "BONI",
    "latitude": 38.7871818482598,
    "longitude": 16.2514499597595
  },
  {
    "name": "BONIFICIO",
    "latitude": 38.5626326081792,
    "longitude": 16.0481569046635
  },
  {
    "name": "BORDILA",
    "latitude": 38.6928407555628,
    "longitude": 15.9545478747665
  },
  {
    "name": "BORDILA",
    "latitude": 38.6897291381767,
    "longitude": 15.9471132800269
  },
  {
    "name": "BORG.A DI PORTO SALVO",
    "latitude": 38.7054310859352,
    "longitude": 16.0836474525351
  },
  {
    "name": "BOSCO ARCHIFORO",
    "latitude": 38.537860593074,
    "longitude": 16.3482796159497
  },
  {
    "name": "BOSCO BIANCHI",
    "latitude": 38.7999262919482,
    "longitude": 16.2990935361463
  },
  {
    "name": "BOSCO DELLA CASTAGNARA",
    "latitude": 38.801287768182,
    "longitude": 16.2426913974104
  },
  {
    "name": "BOSCO DELLE CENTO FONTANE",
    "latitude": 38.6843656540533,
    "longitude": 16.2646028663621
  },
  {
    "name": "BOSCO DI MONTE CUCCO",
    "latitude": 38.6594978827729,
    "longitude": 16.3348237545197
  },
  {
    "name": "BOSCO DI S. GIOVANNI",
    "latitude": 38.6327182507533,
    "longitude": 16.1803903042776
  },
  {
    "name": "BOSCO DI S. MARIA",
    "latitude": 38.5512257061697,
    "longitude": 16.3076760666921
  },
  {
    "name": "BOSCO FIEGO",
    "latitude": 38.6013412423741,
    "longitude": 16.2480203977443
  },
  {
    "name": "BOSCO LACINA",
    "latitude": 38.569580520223,
    "longitude": 16.4199469651847
  },
  {
    "name": "BOSCO MILETO",
    "latitude": 38.529013060734,
    "longitude": 16.0119440465091
  },
  {
    "name": "BOSCO REGGIO",
    "latitude": 38.5098238827427,
    "longitude": 16.2159424511053
  },
  {
    "name": "BOSCO REGGIO",
    "latitude": 38.5737939662709,
    "longitude": 16.411328755876
  },
  {
    "name": "BOSCO RUSSIA",
    "latitude": 38.8143731795343,
    "longitude": 16.2891512217363
  },
  {
    "name": "BOSCO SCALA",
    "latitude": 38.6777954779728,
    "longitude": 16.2563251607315
  },
  {
    "name": "BRATTIRO",
    "latitude": 38.6437526195873,
    "longitude": 15.8872616165125
  },
  {
    "name": "BRIATICO",
    "latitude": 38.7257107071405,
    "longitude": 16.0324400349289
  },
  {
    "name": "BRIATICO VECCHIO",
    "latitude": 38.7068096082389,
    "longitude": 16.0226455370594
  },
  {
    "name": "BRIGA",
    "latitude": 38.5684968613704,
    "longitude": 16.1061860344948
  },
  {
    "name": "BRIGLIA",
    "latitude": 38.6073258318968,
    "longitude": 15.981138227809
  },
  {
    "name": "BRIVADI",
    "latitude": 38.6368882647787,
    "longitude": 15.8566949349794
  },
  {
    "name": "BROGNATURO",
    "latitude": 38.6025475118014,
    "longitude": 16.3409912857703
  },
  {
    "name": "BRUNELLO",
    "latitude": 38.5956663938931,
    "longitude": 16.3578690551391
  },
  {
    "name": "BRUNIA",
    "latitude": 38.6806371283632,
    "longitude": 16.0393452739199
  },
  {
    "name": "BRUNIA",
    "latitude": 38.664397911497,
    "longitude": 16.1362797594401
  },
  {
    "name": "BRUNO GRILLO",
    "latitude": 38.5113039844881,
    "longitude": 16.303684804863
  },
  {
    "name": "BRUVIERE",
    "latitude": 38.7378459398363,
    "longitude": 16.2984742198091
  },
  {
    "name": "BUFFALARIANA",
    "latitude": 38.76191412738,
    "longitude": 16.2138791564872
  },
  {
    "name": "BUNDA",
    "latitude": 38.6386369768428,
    "longitude": 16.2750126557766
  },
  {
    "name": "BUTTINA",
    "latitude": 38.6093307203052,
    "longitude": 16.2634797121273
  },
  {
    "name": "ACERO C.",
    "latitude": 38.4696737463024,
    "longitude": 16.3835171723454
  },
  {
    "name": "ADENCI C.",
    "latitude": 38.7047426538105,
    "longitude": 16.1976279994511
  },
  {
    "name": "AGRILLUSA C.",
    "latitude": 38.6426270414852,
    "longitude": 15.9743574220163
  },
  {
    "name": "AIA BISSATA C.",
    "latitude": 38.5991667696259,
    "longitude": 15.9663970769322
  },
  {
    "name": "ALENTA C.",
    "latitude": 38.7148708185895,
    "longitude": 16.1985676347296
  },
  {
    "name": "ALIVARI C.",
    "latitude": 38.6167531145958,
    "longitude": 15.9526540988728
  },
  {
    "name": "ANELLO C.",
    "latitude": 38.7677301534805,
    "longitude": 16.2777678589575
  },
  {
    "name": "ANGRO C.",
    "latitude": 38.6173533346902,
    "longitude": 16.2472985476818
  },
  {
    "name": "ARCINA C.",
    "latitude": 38.5999614942553,
    "longitude": 16.2404863225242
  },
  {
    "name": "C. AREA DEL BARONE",
    "latitude": 38.5383645117571,
    "longitude": 16.0070380410978
  },
  {
    "name": "C. ARIA",
    "latitude": 38.7906454261029,
    "longitude": 16.231637358424
  },
  {
    "name": "C. BARATTA",
    "latitude": 38.7046855417723,
    "longitude": 16.2145208822861
  },
  {
    "name": "C. BARBIERI",
    "latitude": 38.7037864529261,
    "longitude": 16.1601906917288
  },
  {
    "name": "C. BARBIERI ( DIR.)",
    "latitude": 38.5455003438003,
    "longitude": 15.9795975534375
  },
  {
    "name": "C. BARDO",
    "latitude": 38.615438546182,
    "longitude": 16.2485518066348
  }
];

function haversineDistance(lat1, lon1, lat2, lon2) {
  const toRad = x => x * Math.PI / 180;
  const R = 6371; // Earth radius in km
  const dLat = toRad(lat2 - lat1);
  const dLon = toRad(lon2 - lon1);
  const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function trovaToponimiVicini(lat, lon, count = 20) {
  const distanze = placemarks.map(p => {
    const distanza = haversineDistance(lat, lon, p.latitude, p.longitude);
    return { name: p.name, latitude: p.latitude, longitude: p.longitude, distanza };
  });
  distanze.sort((a, b) => a.distanza - b.distanza);
  return distanze.slice(0, count);
}
