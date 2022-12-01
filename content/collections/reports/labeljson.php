<?php
$LABEL_FORMAT_JSON = '{
    "labelFormats": [
        {
            "title": "Generic Herbarium Label",
            "displaySpeciesAuthor": 1,
            "displayBarcode": 0,
            "labelType": "2",
            "defaultStyles": "font-size:10pt",
            "defaultCss": "..\/..\/css\/symb\/labelhelpers.css",
            "customCss": "",
            "pageSize": "letter",
            "labelHeader": {
                "prefix": "Flora of ",
                "midText": 3,
                "suffix": " county",
                "className": "text-center font-bold font-sans text-2xl",
                "style": "margin-bottom:10px;"
            },
            "labelBlocks": [					
                {
                    "divBlock": {
                        "className": "label-block",
                        "blocks": [
                            {
                                "divBlock": {
                                    "className": "taxonomy my-2 text-lg",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "identificationqualifier"
                                                },
                                                {
                                                    "field": "speciesname",
                                                    "className": "font-bold italic"
                                                },
                                                {
                                                    "field": "parentauthor"
                                                },
                                                {
                                                    "field": "taxonrank",
                                                    "className": "font-bold"
                                                },
                                                {
                                                    "field": "infraspecificepithet",
                                                    "className": "font-bold italic"
                                                },
                                                {
                                                    "field": "scientificnameauthorship"
                                                }
                                            ],
                                            "delimiter": " "
                                        },
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "family",
                                                    "styles": [
                                                        "float:right"
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identifiedby",
                                        "prefix": "Det by: "
                                    },
                                    {
                                        "field": "dateidentified"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identificationreferences"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identificationremarks"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "taxonremarks"
                                    }
                                ]
                            },
                            {
                                "divBlock": {
                                    "className": "text-lg",
                                    "style": "margin-top:10px;",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "country",
                                                    "className": "font-bold"
                                                },
                                                {
                                                    "field": "stateprovince",
                                                    "style": "font-weight:bold"
                                                },
                                                {
                                                    "field": "county"
                                                },
                                                {
                                                    "field": "municipality"
                                                },
                                                {
                                                    "field": "locality"
                                                }
                                            ],
                                            "delimiter": ", "
                                        }
                                    ]
                                }
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "decimallatitude"
                                    },
                                    {
                                        "field": "decimallongitude",
                                        "style": "margin-left:10px"
                                    },
                                    {
                                        "field": "coordinateuncertaintyinmeters",
                                        "prefix": "+-",
                                        "suffix": " meters",
                                        "style": "margin-left:10px"
                                    },
                                    {
                                        "field": "geodeticdatum",
                                        "prefix": "[",
                                        "suffix": "]",
                                        "style": "margin-left:10px"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "verbatimcoordinates"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "elevationinmeters",
                                        "prefix": "Elev: ",
                                        "suffix": "m. "
                                    },
                                    {
                                        "field": "verbatimelevation"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "habitat",
                                        "suffix": "."
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "substrate",
                                        "suffix": "."
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "verbatimattributes"
                                    },
                                    {
                                        "field": "establishmentmeans"
                                    }
                                ],
                                "delimiter": "; "
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "associatedtaxa",
                                        "prefix": "Associated species: ",
                                        "className": "italic"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "occurrenceremarks"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "typestatus"
                                    }
                                ]
                            },
                            {
                                "divBlock": {
                                    "className": "collector",
                                    "style": "margin-top:10px;",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "recordedby",
                                                    "style": "float:left"
                                                },
                                                {
                                                    "field": "recordnumber",
                                                    "style": "float:left;margin-left:10px"
                                                },
                                                {
                                                    "field": "eventdate",
                                                    "style": "float:right"
                                                }
                                            ]
                                        },
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "associatedcollectors",
                                                    "prefix": "with: "
                                                }
                                            ],
                                            "style": "clear:both; margin-left:10px;"
                                        }
                                    ]
                                }
                            }
                        ]
                    }
                }
            ],
            "labelFooter": {
                "textValue": "",
                "className": "text-center font-bold font-sans",
                "style": "margin-top:10px;"
            }
        },
        {
            "title": "Generic Vertebrate Label",
            "displaySpeciesAuthor": 0,
            "displayBarcode": 0,
            "labelType": "3",
            "defaultStyles": "font-size:10pt",
            "defaultCss": "..\/..\/css\/symb\/labelhelpers.css",
            "customCss": "",
            "pageSize": "letter",
            "labelHeader": {
                "prefix": "",
                "midText": 0,
                "suffix": "",
                "className": "text-center font-bold font-sans text-2xl",
                "style": "text-align:center;margin-bottom:5px;font:bold 7pt arial,sans-serif;clear:both;"
            },
            "labelFooter": {
                "textValue": "",
                "className": "text-center font-bold font-sans text-2xl",
                "style": "text-align:center;margin-top:10px;font:bold 10pt arial,sans-serif;clear:both;"
            },
            "labelBlocks": [
                {
                    "divBlock": {
                        "className": "labelBlockDiv",
                        "blocks": [
                            {
                                "fieldBlock": [
                                    {
                                        "field": "family",
                                        "styles": [
                                            "margin-bottom:2px;font-size:pt"
                                        ]
                                    }
                                ]
                            },
                            {
                                "divBlock": {
                                    "className": "taxonomyDiv",
                                    "style": "font-size:10pt;",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "identificationqualifier"
                                                },
                                                {
                                                    "field": "speciesname",
                                                    "style": "font-weight:bold;font-style:italic"
                                                },
                                                {
                                                    "field": "parentauthor"
                                                },
                                                {
                                                    "field": "taxonrank",
                                                    "style": "font-weight:bold"
                                                },
                                                {
                                                    "field": "infraspecificepithet",
                                                    "style": "font-weight:bold;font-style:italic"
                                                },
                                                {
                                                    "field": "scientificnameauthorship"
                                                }
                                            ],
                                            "delimiter": " "
                                        }
                                    ]
                                }
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identifiedby",
                                        "prefix": "Det by: "
                                    },
                                    {
                                        "field": "dateidentified"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identificationreferences"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "identificationremarks"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "taxonremarks"
                                    }
                                ]
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "catalognumber",
                                        "style": "font-weight:bold;font-size:14pt;margin:5pt 0pt;"
                                    }
                                ]
                            },
                            {
                                "divBlock": {
                                    "className": "localDiv",
                                    "style": "margin-top:3px;padding-top:3px;border-top:3px solid black",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "country"
                                                },
                                                {
                                                    "field": "stateprovince",
                                                    "prefix": ", "
                                                },
                                                {
                                                    "field": "county",
                                                    "prefix": ", "
                                                },
                                                {
                                                    "field": "municipality",
                                                    "prefix": ", "
                                                },
                                                {
                                                    "field": "locality",
                                                    "prefix": ": "
                                                },
                                                {
                                                    "field": "decimallatitude",
                                                    "prefix": ": ",
                                                    "suffix": "\u00b0 N"
                                                },
                                                {
                                                    "field": "decimallongitude",
                                                    "prefix": " ",
                                                    "suffix": "\u00b0 W"
                                                },
                                                {
                                                    "field": "coordinateuncertaintyinmeters",
                                                    "prefix": " +-",
                                                    "suffix": " meters",
                                                    "style": "margin-left:10px"
                                                },
                                                {
                                                    "field": "elevationinmeters",
                                                    "prefix": ", ",
                                                    "suffix": "m."
                                                }
                                            ]
                                        }
                                    ]
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "collectorDiv",
                                    "style": "margin-top:10px;font-size:6pt;clear:both;",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "recordedby",
                                                    "style": "float:left;",
                                                    "prefix": "Coll.: "
                                                },
                                                {
                                                    "field": "preparations",
                                                    "style": "float:right",
                                                    "prefix": "Prep.: "
                                                }
                                            ]
                                        }
                                    ]
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "collectorDiv",
                                    "style": "margin-top:10px;font-size:6pt;clear:both;",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "recordnumber",
                                                    "style": "float:left;",
                                                    "prefix": "Coll. No: "
                                                },
                                                {
                                                    "field": "eventdate",
                                                    "style": "float:right",
                                                    "prefix": "Date: "
                                                }
                                            ]
                                        }
                                    ]
                                }
                            }
                        ]
                    }
                }
            ]
        },
        {
            "title": "NEON Herptile (Jar) Label",
            "labelHeader": {
                "prefix": "Arizona State University",
                "midText": "0",
                "suffix": "",
                "className": "font-bold text-base text-align-center",
                "style": ""
            },
            "labelFooter": {
                "textValue": "",
                "className": "",
                "style": ""
            },
            "customStyles": "body {font-family:Arial,sans-serif;font-size:7pt} .label {padding:8pt; }",
            "defaultCss": "..\/..\/css\/symb\/labelhelpers.css",
            "customCss": "..\/..\/content\/collections\/reports\/neon.herpjar.css",
            "customJS": "..\/..\/content\/collections\/reports\/neon.herpjar.js",
            "labelType": "2",
            "pageSize": "letter",
            "displaySpeciesAuthor": 0,
            "displayBarcode": 0,
            "labelBlocks": [
								{
										"divBlock": {
												"className": "col-title font-bold text-align-center",
												"content": "NEON Biorepository Herptile Collection",
												"style": "font-size:6pt"
										}
								},							
                {
                    "divBlock": {
                        "className": "label-blocks",
                        "style": "",
                        "blocks": [
                            {
                                "fieldBlock": [
                                    {
                                        "field": "family"
                                    }
                                ],
                                "delimiter": " ",
																"className": "mt-2"
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "scientificname",
                                        "className": "font-bold italic",
																				"style": "font-size:8pt"
                                    }
                                ],
                                "delimiter": " ",
                                "className": "bar my-4"
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "year",
                                        "className": "font-bold",
																				"style": "font-size:8pt"
                                    }
                                ],
                                "delimiter": " "
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "locality"
                                    }
                                ],
                                "delimiter": " "
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "preparations",
                                        "prefix": "Prep.: "
                                    }
																	],
																	"delimiter": " ",
																	"className": "my-4"
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "catalognumber",
																				"style": "font-size:6pt"
                                    }
                                ],
                                "delimiter": " "
                            },
                            {
                                "fieldBlock": [
                                    {
                                        "field": "othercatalognumbers",
																				"style": "font-size:6pt"
                                    }
                                ],
                                "delimiter": " "
                            }
                        ]
                    }
                }
            ]
        },
        {
            "title": "NEON Herptile (Vial) Label",
            "labelHeader": {
                "prefix": "Arizona State University",
                "midText": "0",
                "suffix": "",
                "className": "font-bold text-base text-align-center",
                "style": "font-size:7pt"
            },
            "labelFooter": {
                "textValue": "",
                "className": "",
                "style": ""
            },
            "customStyles": "",
            "defaultCss": "..\/..\/css\/symb\/labelhelpers.css",
            "customCss": "..\/..\/content\/collections\/reports\/neon.mamskull.css",
            "customJS": "..\/..\/content\/collections\/reports\/neon.herp.js?v=202209",
            "labelType": "3",
            "pageSize": "letter",
            "displaySpeciesAuthor": 0,
            "displayBarcode": 1,
            "labelBlocks": [
                {
                    "divBlock": {
                        "className": "label-blocks",
                        "style": "",
                        "blocks": [
                            {
                                "divBlock": {
                                    "className": "col-title font-bold text-align-center",
                                    "style": "font-size:6pt",
                                    "content": "NEON Biorepository Herptile Collection"
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "label-top",
                                    "style": "",
                                    "blocks": [
                                        {
                                            "divBlock": {
                                                "className": "cat-nums my-2",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "catalognumber",
                                                                "className": "font-bold block text-align-center",
                                                                "style": "font-size:8pt"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        },
                                        {
                                            "divBlock": {
                                                "className": "taxonomy mb-2",
                                                "style": "",
                                                "delimiter": " ",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "identificationqualifier",
                                                                "style": "margin-right: 4pt"
                                                            },
                                                            {
                                                                "field": "speciesname",
                                                                "className": "font-bold italic"
                                                            },
                                                            {
                                                                "field": "taxonrank"
                                                            },
                                                            {
                                                                "field": "infraspecificepithet",
                                                                "className": "font-bold italic"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        },
                                        {
                                            "divBlock": {
                                                "className": "local my-4",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "country",
                                                                "className": ""
                                                            },
                                                            {
                                                                "field": "stateprovince",
                                                                "prefix": ": ",
                                                                "className": ""
                                                            },
                                                            {
                                                                "field": "county",
                                                                "prefix": "; "
                                                            },
                                                            {
                                                                "field": "municipality",
                                                                "prefix": ", ",
                                                                "className": ""
                                                            },
                                                            {
                                                                "field": "locality",
                                                                "className": "",
                                                                "prefix": ", ",
                                                                "suffix": "."
                                                            },
                                                            {
                                                                "field": "decimallatitude",
                                                                "prefix": " ",
                                                                "suffix": "N"
                                                            },
                                                            {
                                                                "field": "decimallongitude",
                                                                "prefix": ", ",
                                                                "suffix": "W"
                                                            },
                                                            {
                                                                "field": "coordinateuncertaintyinmeters",
                                                                "prefix": "+-",
                                                                "suffix": " meters.",
                                                                "style": ""
                                                            },
                                                            {
                                                                "field": "elevationinmeters",
                                                                "prefix": " Elev: ",
                                                                "suffix": "m.",
                                                                "className": ""
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "divBlock": {
                                                            "className": "life-sex my-2",
                                                            "blocks": [
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "lifestage",
                                                                            "prefix": "Life stage: ",
                                                                            "suffix": ".",
                                                                            "className": "capitalize"
                                                                        },
                                                                        {
                                                                            "field": "sex",
                                                                            "prefix": " Sex: ",
                                                                            "suffix": ".",
                                                                            "className": ""
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        }
                                                    },
                                                    {
                                                        "divBlock": {
                                                            "className": "event",
                                                            "blocks": [
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "eventdate",
                                                                            "prefix": " Date: "
                                                                        }
                                                                    ],
                                                                    "className": "mb-4"
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "recordedby",
                                                                            "className": "",
                                                                            "prefix": "Coll. by: "
                                                                        },
                                                                        {
                                                                            "field": "recordnumber",
                                                                            "className": "",
                                                                            "prefix": "Collector Number "
                                                                        }
                                                                    ],
                                                                    "className": ""
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "identifiedby",
                                                                            "className": "",
                                                                            "prefix": "Det. by: "
                                                                        },
                                                                        {
                                                                            "field": "dateidentified",
                                                                            "prefix": " (",
                                                                            "suffix": ")"
                                                                        }
                                                                    ],
                                                                    "className": "hidden"
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "dynamicproperties",
                                                                            "className": "block"
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "preparations",
                                                                            "className": "block"
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "individualCount",
                                                                            "className": "",
                                                                            "prefix": " Count:"
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        }
                                                    }
                                                ]
                                            }
                                        }
                                    ]
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "label-bottom mt-2",
                                    "style": "",
                                    "blocks": [
                                        {
                                            "divBlock": {
                                                "className": "event",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "othercatalognumbers",
                                                                "className": "font-normal block",
                                                                "style": ""
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        }
                                    ]
                                }
                            }
                        ]
                    }
                }
            ]
        },
        {
            "title": "NEON Mammal (Wet) Label - CLONE",
            "labelHeader": {
                "prefix": "Arizona State University",
                "midText": "0",
                "suffix": "",
                "className": "font-bold text-base text-align-center",
                "style": ""
            },
            "labelFooter": {
                "textValue": "",
                "className": "",
                "style": ""
            },
            "customStyles": "body {font-family:Arial,sans-serif;font-size:8pt} .label {padding:8pt; }",
            "defaultCss": "..\/..\/css\/symb\/labelhelpers.css",
            "customCss": "..\/..\/content\/collections\/reports\/neon.mamwet.barcode.css",
            "customJS": "..\/..\/content\/collections\/reports\/neon.mamwet.js",
            "labelType": "2",
            "pageSize": "letter",
            "displaySpeciesAuthor": 0,
            "displayBarcode": 1,
            "labelBlocks": [
                {
                    "divBlock": {
                        "className": "label-blocks",
                        "style": "",
                        "blocks": [
                            {
                                "divBlock": {
                                    "className": "col-title font-bold text-align-center",
                                    "content": "NEON Biorepository Mammal Collection"
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "label-top",
                                    "style": "",
                                    "blocks": [
                                        {
                                            "fieldBlock": [
                                                {
                                                    "field": "family",
                                                    "className": "text-sm font-normal mb-1"
                                                }
                                            ]
                                        },
                                        {
                                            "divBlock": {
                                                "className": "taxonomy",
                                                "style": "",
                                                "delimiter": " ",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "identificationqualifier",
                                                                "style": "margin-right: 4pt"
                                                            },
                                                            {
                                                                "field": "speciesname",
                                                                "className": "text-2xl font-bold italic"
                                                            },
                                                            {
                                                                "field": "parentauthor"
                                                            },
                                                            {
                                                                "field": "taxonrank"
                                                            },
                                                            {
                                                                "field": "infraspecificepithet",
                                                                "className": "font-bold italic"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        },
                                        {
                                            "divBlock": {
                                                "className": "cat-nums bar mb-4",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "catalognumber",
                                                                "className": "text-2xl font-bold block"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        },
                                        {
                                            "divBlock": {
                                                "className": "local mt-2",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "country",
                                                                "className": "font-bold"
                                                            },
                                                            {
                                                                "field": "stateprovince",
                                                                "prefix": ", ",
                                                                "className": "font-bold"
                                                            },
                                                            {
                                                                "field": "county",
                                                                "prefix": ", "
                                                            },
                                                            {
                                                                "field": "municipality",
                                                                "prefix": ", ",
                                                                "className": "font-bold"
                                                            },
                                                            {
                                                                "field": "locality",
                                                                "className": "",
                                                                "prefix": ", ",
                                                                "suffix": "."
                                                            },
                                                            {
                                                                "field": "decimallatitude",
                                                                "prefix": " ",
                                                                "suffix": "N"
                                                            },
                                                            {
                                                                "field": "decimallongitude",
                                                                "prefix": ", ",
                                                                "suffix": "W"
                                                            },
                                                            {
                                                                "field": "coordinateuncertaintyinmeters",
                                                                "prefix": "+-",
                                                                "suffix": " meters.",
                                                                "style": ""
                                                            },
                                                            {
                                                                "field": "elevationinmeters",
                                                                "prefix": " Elev: ",
                                                                "suffix": "m.",
                                                                "className": ""
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        "divBlock": {
                                                            "className": "life-sex",
                                                            "blocks": [
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "lifestage",
                                                                            "prefix": "Life stage: ",
                                                                            "suffix": ".",
                                                                            "className": "capitalize mb-2"
                                                                        },
                                                                        {
                                                                            "field": "sex",
                                                                            "prefix": " Sex: ",
                                                                            "suffix": ".",
                                                                            "className": "mb-2"
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        }
                                                    },
                                                    {
                                                        "divBlock": {
                                                            "className": "event grid grid-cols-2 mt-2",
                                                            "blocks": [
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "recordedby",
                                                                            "className": "",
                                                                            "prefix": "Collector: "
                                                                        },
                                                                        {
                                                                            "field": "recordnumber",
                                                                            "className": "",
                                                                            "prefix": "Collector Number "
                                                                        },
                                                                        {
                                                                            "field": "dynamicproperties",
                                                                            "className": "block"
                                                                        }
                                                                    ]
                                                                },
                                                                {
                                                                    "fieldBlock": [
                                                                        {
                                                                            "field": "eventdate",
                                                                            "prefix": " Date: "
                                                                        },
                                                                        {
                                                                            "field": "preparations",
                                                                            "className": "block",
                                                                            "prefix": "Prep.: "
                                                                        }
                                                                    ],
                                                                    "className": "text-align-right"
                                                                }
                                                            ]
                                                        }
                                                    }
                                                ]
                                            }
                                        }
                                    ]
                                }
                            },
                            {
                                "divBlock": {
                                    "className": "label-bottom mt-2",
                                    "style": "",
                                    "blocks": [
                                        {
                                            "divBlock": {
                                                "className": "event text-base",
                                                "blocks": [
                                                    {
                                                        "fieldBlock": [
                                                            {
                                                                "field": "othercatalognumbers",
                                                                "className": "text-lg font-normal block"
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        }
                                    ]
                                }
                            }
                        ]
                    }
                }
            ]
        }
    ]
}';
