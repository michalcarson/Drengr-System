import React from "react";

const MemberCertifications = (props) => {
    return (
        <ul>
            {props.certifications.map((cert) => (
                <li>
                    {cert.name}
                </li>
            ))}
        </ul>
    );
};

export default MemberCertifications;